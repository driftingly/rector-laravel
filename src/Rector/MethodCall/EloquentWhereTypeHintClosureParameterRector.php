<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\EloquentWhereTypeHintClosureParameterRectorTest
 */
class EloquentWhereTypeHintClosureParameterRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change typehint of closure parameter in where method of Eloquent Builder',
            [
                new CodeSample(<<<'CODE_SAMPLE'
$query->where(function ($query) {
    $query->where('id', 1);
});
CODE_SAMPLE, <<<'CODE_SAMPLE'
$query->where(function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('id', 1);
});
CODE_SAMPLE),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof MethodCall && ! $node instanceof StaticCall) {
            return null;
        }

        if ($this->isWhereMethodWithClosureOrArrowFunction($node)) {
            $this->changeClosureParamType($node);

            return $node;
        }

        return null;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function isWhereMethodWithClosureOrArrowFunction($node): bool
    {
        if (! $this->expectedObjectTypeAndMethodCall($node)) {
            return false;
        }

        return ! (! ($node->getArgs()[0]->value ?? null) instanceof Closure &&
        ! ($node->getArgs()[0]->value ?? null) instanceof ArrowFunction);
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function changeClosureParamType($node): void
    {
        /** @var ArrowFunction|Closure $closure */
        $closure = $node->getArgs()[0]
            ->value;

        if (! isset($closure->getParams()[0])) {
            return;
        }

        $param = $closure->getParams()[0];

        if ($param->type instanceof Name) {
            return;
        }

        $param->type = new FullyQualified('Illuminate\Contracts\Database\Query\Builder');
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function expectedObjectTypeAndMethodCall($node): bool
    {
        switch (true) {
            case $node instanceof MethodCall && $this->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Contracts\Database\Query\Builder')
            ):
                return true;
            case $node instanceof StaticCall && $this->isObjectType(
                $node->class,
                new ObjectType('Illuminate\Database\Eloquent\Model')
            ):
                return true;
            default:
                return false;
        }
    }
}

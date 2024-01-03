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
use Rector\Core\Rector\AbstractRector;
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
                new CodeSample(
                    <<<'CODE_SAMPLE'
$query->where(function ($query) {
    $query->where('id', 1);
});
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$query->where(function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('id', 1);
});
CODE_SAMPLE
                    ,
                ),
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

    private function isWhereMethodWithClosureOrArrowFunction(MethodCall|StaticCall $node): bool
    {
        if (! $this->expectedObjectTypeAndMethodCall($node)) {
            return false;
        }

        return ! (! ($node->getArgs()[0]->value ?? null) instanceof Closure &&
        ! ($node->getArgs()[0]->value ?? null) instanceof ArrowFunction);
    }

    private function changeClosureParamType(MethodCall|StaticCall $node): void
    {
        /** @var Node\Expr\ArrowFunction|Node\Expr\Closure $closure */
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

    private function expectedObjectTypeAndMethodCall(MethodCall|StaticCall $node): bool
    {
        return match (true) {
            $node instanceof MethodCall && $this->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Contracts\Database\Query\Builder')
            ) => true,
            $node instanceof StaticCall && $this->isObjectType(
                $node->class,
                new ObjectType('Illuminate\Database\Eloquent\Model')
            ) => true,
            default => false,
        } && $this->isNames($node->name, ['where', 'orWhere']);
    }
}

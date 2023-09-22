<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
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
        return [Node\Expr\MethodCall::class, Node\Expr\StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof Node\Expr\MethodCall && ! $node instanceof Node\Expr\StaticCall) {
            return null;
        }

        if ($this->isWhereMethodWithClosureOrArrowFunction($node)) {
            $this->changeClosureParamType($node);

            return $node;
        }

        return null;
    }

    private function isWhereMethodWithClosureOrArrowFunction(
        Node\Expr\MethodCall|Node\Expr\StaticCall $node
    ): bool {
        if (! $this->expectedObjectTypeAndMethodCall($node)) {
            return false;
        }

        if (
            ! ($node->getArgs()[0]->value ?? null) instanceof Node\Expr\Closure &&
            ! ($node->getArgs()[0]->value ?? null) instanceof Node\Expr\ArrowFunction
        ) {
            return false;
        }

        return true;
    }

    private function changeClosureParamType(Node\Expr\MethodCall|Node\Expr\StaticCall $node): void
    {
        /** @var Node\Expr\ArrowFunction|Node\Expr\Closure $closure */
        $closure = $node->getArgs()[0]
            ->value;

        if (! isset($closure->getParams()[0])) {
            return;
        }

        $param = $closure->getParams()[0];

        if ($param->type instanceof Node\Name) {
            return;
        }

        $param->type = new Node\Name\FullyQualified('Illuminate\Contracts\Database\Query\Builder');
    }

    private function expectedObjectTypeAndMethodCall(Node\Expr\MethodCall|Node\Expr\StaticCall $node): bool
    {
        return match (true) {
            $node instanceof Node\Expr\MethodCall && $this->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Contracts\Database\Query\Builder')
            ) => true,
            $node instanceof Node\Expr\StaticCall && $this->isObjectType(
                $node->class,
                new ObjectType('Illuminate\Database\Eloquent\Model')
            ) => true,
            default => false,
        } && $this->isNames($node->name, ['where', 'orWhere']);
    }
}

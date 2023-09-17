<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\EloquentWhereRelationTypeHintingParameterRectorTest
 */
class EloquentWhereRelationTypeHintingParameterRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add type hinting to where relation has methods e.g. whereHas, orWhereHas, whereDoesntHave, orWhereDoesntHave, whereHasMorph, orWhereHasMorph, whereDoesntHaveMorph, orWhereDoesntHaveMorph',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
User::whereHas('posts', function ($query) {
    $query->where('is_published', true);
});

$query->whereHas('posts', function ($query) {
    $query->where('is_published', true);
});
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
User::whereHas('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});

$query->whereHas('posts', function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('is_published', true);
});
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class, Node\Expr\StaticCall::class];
    }

    /**
     * @param Node\Expr\MethodCall|Node\Expr\StaticCall $node
     * @return Node\Expr\MethodCall|Node\Expr\StaticCall|null
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->isWhereHasClosureOrArrowFunction($node)) {
            $this->changeClosureParamType($node);
        }

        return $node;
    }

    private function isWhereHasClosureOrArrowFunction(Node $node): bool
    {
        if (! $this->expectedObjectTypeAndMethodCall($node)) {
            return false;
        }

        if (! isset($node->args[1])) {
            return false;
        }

        if (! $node->args[1]->value instanceof Node\Expr\Closure && ! $node->args[1]->value instanceof Node\Expr\ArrowFunction) {
            return false;
        }

        return true;
    }

    /**
     * @param Node\Expr\MethodCall|Node\Expr\StaticCall $node
     */
    private function changeClosureParamType(Node $node): void
    {
        $closure = $node->getArgs()[1]
->value;

        if (! isset($closure->params[0])) {
            return;
        }

        $param = $closure->params[0];

        if ($param->type instanceof Node\Name) {
            return;
        }

        $param->type = new Node\Name\FullyQualified('Illuminate\Contracts\Database\Eloquent\Builder');
    }

    private function expectedObjectTypeAndMethodCall(Node $node): bool
    {
        return ($node instanceof Node\Expr\MethodCall && $this->isObjectType(
            $node->var,
            new ObjectType('Illuminate\Database\Eloquent\Builder')
        ) && $this->isNames(
            $node->name,
            [
                'whereHas',
                'orWhereHas',
                'whereDoesntHave',
                'orWhereDoesntHave',
                'whereHasMorph',
                'orWhereHasMorph',
                'whereDoesntHaveMorph',
                'orWhereDoesntHaveMorph',
            ]
        )) ||
            ($node instanceof Node\Expr\StaticCall && $this->isObjectType(
                $node->class,
                new ObjectType('Illuminate\Database\Eloquent\Model')
            ) && $this->isNames(
                $node->name,
                [
                    'whereHas',
                    'orWhereHas',
                    'whereDoesntHave',
                    'orWhereDoesntHave',
                    'whereHasMorph',
                    'orWhereHasMorph',
                    'whereDoesntHaveMorph',
                    'orWhereDoesntHaveMorph',
                ]
            ));
    }
}

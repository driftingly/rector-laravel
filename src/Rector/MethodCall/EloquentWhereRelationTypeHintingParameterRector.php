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

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof Node\Expr\MethodCall && ! $node instanceof Node\Expr\StaticCall) {
            return null;
        }

        if ($this->isWhereRelationMethodWithClosureOrArrowFunction($node)) {
            $this->changeClosureParamType($node);

            return $node;
        }

        return null;
    }

    private function isWhereRelationMethodWithClosureOrArrowFunction(
        Node\Expr\MethodCall|Node\Expr\StaticCall $node
    ): bool {
        if (! $this->expectedObjectTypeAndMethodCall($node)) {
            return false;
        }

        if (
            ! ($node->getArgs()[1]->value ?? null) instanceof Node\Expr\Closure &&
            ! ($node->getArgs()[1]->value ?? null) instanceof Node\Expr\ArrowFunction
        ) {
            return false;
        }

        return true;
    }

    private function changeClosureParamType(Node\Expr\MethodCall|Node\Expr\StaticCall $node): void
    {
        /** @var Node\Expr\ArrowFunction|Node\Expr\Closure $closure */
        $closure = $node->getArgs()[1]
->value;

        if (! isset($closure->getParams()[0])) {
            return;
        }

        $param = $closure->getParams()[0];

        if ($param->type instanceof Node\Name) {
            return;
        }

        $param->type = new Node\Name\FullyQualified('Illuminate\Contracts\Database\Eloquent\Builder');
    }

    private function expectedObjectTypeAndMethodCall(Node\Expr\MethodCall|Node\Expr\StaticCall $node): bool
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

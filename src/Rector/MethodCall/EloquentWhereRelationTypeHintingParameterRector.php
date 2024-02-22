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
 * @deprecated use type-declaration set instead
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\EloquentWhereRelationTypeHintingParameterRectorTest
 */
class EloquentWhereRelationTypeHintingParameterRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add type hinting to where relation has methods e.g. `whereHas`, `orWhereHas`, `whereDoesntHave`, `orWhereDoesntHave`, `whereHasMorph`, `orWhereHasMorph`, `whereDoesntHaveMorph`, `orWhereDoesntHaveMorph`',
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
User::whereHas('posts', function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('is_published', true);
});

$query->whereHas('posts', function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('is_published', true);
});
CODE_SAMPLE
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

        if ($this->isWhereRelationMethodWithClosureOrArrowFunction($node)) {
            $this->changeClosureParamType($node);

            return $node;
        }

        return null;
    }

    private function isWhereRelationMethodWithClosureOrArrowFunction(MethodCall|StaticCall $node): bool
    {
        if (! $this->expectedObjectTypeAndMethodCall($node)) {
            return false;
        }

        // Morph methods have the closure in the 3rd position, others use the 2nd.
        $position = $this->isNames(
            $node->name,
            ['whereHasMorph', 'orWhereHasMorph', 'whereDoesntHaveMorph', 'orWhereDoesntHaveMorph']
        ) ? 2 : 1;

        return ! (! ($node->getArgs()[$position]->value ?? null) instanceof Closure &&
        ! ($node->getArgs()[$position]->value ?? null) instanceof ArrowFunction);
    }

    private function changeClosureParamType(MethodCall|StaticCall $node): void
    {
        // Morph methods have the closure in the 3rd position, others use the 2nd.
        $position = $this->isNames(
            $node->name,
            ['whereHasMorph', 'orWhereHasMorph', 'whereDoesntHaveMorph', 'orWhereDoesntHaveMorph']
        ) ? 2 : 1;

        /** @var ArrowFunction|Closure $closure */
        $closure = $node->getArgs()[$position]
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
        } && $this->isNames(
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
        );
    }
}

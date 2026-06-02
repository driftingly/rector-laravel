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
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\QueryBuilderAnalyzer;
use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\EloquentWhereRelationTypeHintingParameterRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see EloquentWhereRelationTypeHintingParameterRectorTest
 */
class EloquentWhereRelationTypeHintingParameterRector extends AbstractRector
{
    /**
     * @var string[]
     */
    private const array METHODS = [
        'whereHas',
        'orWhereHas',
        'whereDoesntHave',
        'orWhereDoesntHave',
        'whereHasMorph',
        'orWhereHasMorph',
        'whereDoesntHaveMorph',
        'orWhereDoesntHaveMorph',
    ];

    public function __construct(private readonly QueryBuilderAnalyzer $queryBuilderAnalyzer) {}

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

    /**
     * @param  MethodCall|StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->isWhereRelationMethodWithClosureOrArrowFunction($node)) {
            return $this->changeClosureParamType($node);
        }

        return null;
    }

    private function isWhereRelationMethodWithClosureOrArrowFunction(MethodCall|StaticCall $node): bool
    {
        if (! $this->expectedObjectTypeAndMethodCall($node)) {
            return false;
        }

        $position = $this->getPosition($node);

        return ! (! ($node->getArgs()[$position]->value ?? null) instanceof Closure &&
        ! ($node->getArgs()[$position]->value ?? null) instanceof ArrowFunction);
    }

    private function changeClosureParamType(MethodCall|StaticCall $node): ?Node
    {
        $position = $this->getPosition($node);

        /** @var ArrowFunction|Closure $closure */
        $closure = $node->getArgs()[$position]->value;

        if (! isset($closure->getParams()[0])) {
            return null;
        }

        $param = $closure->getParams()[0];

        if ($param->type instanceof Name) {
            return null;
        }

        $param->type = new FullyQualified('Illuminate\Contracts\Database\Query\Builder');

        return $node;
    }

    private function expectedObjectTypeAndMethodCall(MethodCall|StaticCall $node): bool
    {
        foreach (self::METHODS as $method) {
            if ($this->queryBuilderAnalyzer->isMatchingCall($node, $method)) {
                return true;
            }
        }

        return false;
    }

    private function getPosition(MethodCall|StaticCall $node): int
    {
        // Morph methods have the closure in the 3rd position, others use the 2nd.
        return $this->isNames(
            $node->name,
            ['whereHasMorph', 'orWhereHasMorph', 'whereDoesntHaveMorph', 'orWhereDoesntHaveMorph']
        ) ? 2 : 1;
    }
}

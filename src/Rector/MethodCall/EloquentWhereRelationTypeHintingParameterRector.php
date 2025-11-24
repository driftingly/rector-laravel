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
use PHPStan\Type\Type;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\QueryBuilderAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\EloquentWhereRelationTypeHintingParameterRectorTest
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
    public function refactor(Node $node): MethodCall|StaticCall|null
    {
        $type = new ObjectType('Illuminate\Database\Eloquent\Builder');

        if ($node instanceof MethodCall) {
            $type = $this->getType($node->var);
        }

        /** @phpstan-ignore argument.type */
        if ($this->isWhereRelationMethodWithClosureOrArrowFunction($node) && $this->changeClosureParamType($node, $type)) {
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

    /**
     * @param  ObjectType  $type
     */
    private function changeClosureParamType(MethodCall|StaticCall $node, Type $type): bool
    {
        // Morph methods have the closure in the 3rd position, others use the 2nd.
        $position = $this->isNames(
            $node->name,
            ['whereHasMorph', 'orWhereHasMorph', 'whereDoesntHaveMorph', 'orWhereDoesntHaveMorph']
        ) ? 2 : 1;

        /** @var ArrowFunction|Closure $closure */
        $closure = $node->getArgs()[$position]->value;

        if (! isset($closure->getParams()[0])) {
            return false;
        }

        $param = $closure->getParams()[0];

        if ($param->type instanceof Name) {
            return false;
        }

        if ($type->isObject()->no()) {
            return false;
        }

        $param->type = new FullyQualified($type->getClassName());

        return true;
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
}

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
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\QueryBuilderAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\EloquentWhereTypeHintClosureParameterRectorTest
 */
class EloquentWhereTypeHintClosureParameterRector extends AbstractRector
{
    /**
     * @readonly
     */
    private QueryBuilderAnalyzer $queryBuilderAnalyzer;
    public function __construct(QueryBuilderAnalyzer $queryBuilderAnalyzer)
    {
        $this->queryBuilderAnalyzer = $queryBuilderAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change typehint of closure parameter in where method of Eloquent or Query Builder',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where(function ($query) {
    $query->where('id', 1);
});
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1);
});
CODE_SAMPLE
                    ,
                ),
                new CodeSample(
                    <<<'CODE_SAMPLE'
/** @var \Illuminate\Contracts\Database\Eloquent\Builder $query */
$query->where(function ($query) {
    $query->where('id', 1);
});
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
/** @var \Illuminate\Contracts\Database\Eloquent\Builder $query */
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

        $classOrVar = $node instanceof MethodCall
            ? $node->var
            : $node->class;

        $type = $this->isObjectType($classOrVar, new ObjectType('Illuminate\Database\Eloquent\Model'))
            ? 'Illuminate\Contracts\Database\Eloquent\Builder'
            : 'Illuminate\Contracts\Database\Query\Builder';

        $param->type = new FullyQualified($type);
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function expectedObjectTypeAndMethodCall($node): bool
    {
        return $this->queryBuilderAnalyzer->isMatchingCall($node, 'where')
            || $this->queryBuilderAnalyzer->isMatchingCall($node, 'orWhere');
    }
}

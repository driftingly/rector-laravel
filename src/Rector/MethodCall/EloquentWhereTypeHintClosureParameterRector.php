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
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\EloquentWhereTypeHintClosureParameterRectorTest
 */
class EloquentWhereTypeHintClosureParameterRector extends AbstractRector
{
    public function __construct(private readonly QueryBuilderAnalyzer $queryBuilderAnalyzer) {}

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

    /**
     * @param  StaticCall|MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        $type = new ObjectType('Illuminate\Contracts\Database\Eloquent\Builder');

        if ($node instanceof MethodCall) {
            $type = $this->getType($node->var);
        }

        /** @phpstan-ignore argument.type */
        if ($this->isWhereMethodWithClosureOrArrowFunction($node) && $this->changeClosureParamType($node, $type)) {
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

    /**
     * @param  ObjectType  $type
     */
    private function changeClosureParamType(MethodCall|StaticCall $node, Type $type): bool
    {
        /** @var ArrowFunction|Closure $closure */
        $closure = $node->getArgs()[0]
            ->value;

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
        return $this->queryBuilderAnalyzer->isMatchingCall($node, 'where')
            || $this->queryBuilderAnalyzer->isMatchingCall($node, 'orWhere');
    }
}

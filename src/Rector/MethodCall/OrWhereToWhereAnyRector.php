<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\Value\ValueResolver;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\OrWhereToWhereAnyRectorTest
 */
final class OrWhereToWhereAnyRector extends AbstractRector
{
    public function __construct(
        private readonly ValueResolver $valueResolver
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Transforms sequences of orWhere() calls into whereAny() in query builder.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$query->where('active', true)
    ->orWhere('name', 'LIKE', 'Example%')
    ->orWhere('email', 'LIKE', 'Example%')
    ->orWhere('phone', 'LIKE', 'Example%')
    ->get();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$query->where('active', true)
    ->whereAny(['name', 'email', 'phone'], 'LIKE', 'Example%')
    ->get();
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        if (! $this->isName($node->name, 'orWhere')) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Contracts\Database\Query\Builder'))) {
            return null;
        }

        // Get all orWhere calls in sequence
        $orWhereCalls = [];
        $current = $node;
        $baseQuery = null;

        while ($current instanceof MethodCall) {
            if ($this->isName($current->name, 'orWhere')) {
                if (! $this->isValidOrWhereCall($current)) {
                    return null;
                }
                $orWhereCalls[] = $current;
                $baseQuery = $current->var;
            } else {
                $baseQuery = $current;
                break;
            }
            $current = $current->var;
        }

        // Need at least 2 orWhere calls to transform
        if (count($orWhereCalls) < 2) {
            return null;
        }

        // Check if all orWhere calls use the same operator and value
        $methodCall = end($orWhereCalls);
        if (! $this->isValidOrWhereCall($methodCall)) {
            return null;
        }

        $operator = $this->getOperator($methodCall);
        $value = $this->getValue($methodCall);
        if (! $value instanceof Expr) {
            return null;
        }

        $columnsArray = new Array_;

        foreach (array_reverse($orWhereCalls) as $call) {
            if (! $this->isValidOrWhereCall($call)) {
                return null;
            }

            $currentOperator = $this->getOperator($call);
            $currentValue = $this->getValue($call);
            $column = $this->getColumn($call);

            if ($currentOperator !== $operator ||
                ! $currentValue instanceof Expr ||
                ! $column instanceof Expr ||
                ! $this->nodeComparator->areNodesEqual($currentValue, $value)) {
                return null;
            }

            $columnsArray->items[] = new ArrayItem($column);
        }

        return $this->nodeFactory->createMethodCall(
            $baseQuery,
            'whereAny',
            [$columnsArray, $operator, $value]
        );
    }

    private function isValidOrWhereCall(MethodCall $methodCall): bool
    {
        if (count($methodCall->args) < 2) {
            return false;
        }

        foreach ($methodCall->args as $arg) {
            if (! $arg instanceof Arg) {
                return false;
            }
        }

        return true;
    }

    private function getOperator(MethodCall $methodCall): string
    {
        if (! isset($methodCall->args[1]) || ! $methodCall->args[1] instanceof Arg) {
            return '=';
        }

        if (count($methodCall->args) === 2) {
            return '=';
        }

        $operatorValue = $this->valueResolver->getValue($methodCall->args[1]->value);

        return is_string($operatorValue) ? $operatorValue : '=';
    }

    private function getValue(MethodCall $methodCall): ?Expr
    {
        if (! isset($methodCall->args[1]) || ! $methodCall->args[1] instanceof Arg) {
            return null;
        }

        $valueArg = count($methodCall->args) === 2 ? $methodCall->args[1] : $methodCall->args[2];
        if (! $valueArg instanceof Arg) {
            return null;
        }

        return $valueArg->value;
    }

    private function getColumn(MethodCall $methodCall): ?Expr
    {
        if (! isset($methodCall->args[0]) || ! $methodCall->args[0] instanceof Arg) {
            return null;
        }

        return $methodCall->args[0]->value;
    }
}

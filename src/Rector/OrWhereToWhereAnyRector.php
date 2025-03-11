<?php

declare(strict_types=1);

namespace RectorLaravel\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use Rector\PhpParser\Node\Value\ValueResolver;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Transforms sequences of orWhere() calls into whereAny() for better readability and performance
 *
 * @see \RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\OrWhereToWhereAnyRectorTest
 */
final class OrWhereToWhereAnyRector extends AbstractRector
{
    public function __construct(
        private readonly ValueResolver $valueResolver,
    ) {}

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        // Skip if this isn't an orWhere call
        if (! $this->isName($node->name, 'orWhere')) {
            return null;
        }

        // Get all orWhere calls in sequence
        $orWhereCalls = [];
        $current = $node;
        $baseQuery = null;

        while ($current instanceof MethodCall) {
            if ($this->isName($current->name, 'orWhere')) {
                $args = $current->args;
                if (count($args) < 2) {
                    return null;
                }
                $orWhereCalls[] = $current;
            } else {
                $baseQuery = $current;
                break;
            }
            $current = $current->var;
        }

        // Need at least 2 orWhere calls to transform
        if (count($orWhereCalls) < 2 || ! $baseQuery) {
            return null;
        }

        // Check if all orWhere calls use the same operator and value
        $firstCall = end($orWhereCalls);
        $operator = count($firstCall->args) === 2 ? '=' : $this->valueResolver->getValue($firstCall->args[1]->value);
        $value = count($firstCall->args) === 2 ? $firstCall->args[1]->value : $firstCall->args[2]->value;

        // Create the array of column names
        $columnsArray = new Array_;
        foreach (array_reverse($orWhereCalls) as $call) {
            $args = $call->args;
            $currentOperator = count($args) === 2 ? '=' : $this->valueResolver->getValue($args[1]->value);
            $currentValue = count($args) === 2 ? $args[1]->value : $args[2]->value;

            if ($currentOperator !== $operator || ! $this->nodeComparator->areNodesEqual($currentValue, $value)) {
                return null;
            }

            $columnsArray->items[] = new ArrayItem($args[0]->value);
        }

        return $this->nodeFactory->createMethodCall(
            $baseQuery,
            'whereAny',
            [$columnsArray, $operator, $value]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Transforms sequences of orWhere() calls into whereAny() for better readability and performance',
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
}

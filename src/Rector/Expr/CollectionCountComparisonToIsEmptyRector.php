<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\Int_;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Expr\CollectionCountComparisonToIsEmptyRector\CollectionCountComparisonToIsEmptyRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see CollectionCountComparisonToIsEmptyRectorTest
 */
final class CollectionCountComparisonToIsEmptyRector extends AbstractRector
{
    /**
     * Comparisons written as `$collection->count() <operator> <int>`, mapped to the equivalent method.
     *
     * @var array<class-string<BinaryOp>, array<int, string>>
     */
    private const array METHOD_BY_OPERATOR_AND_NUMBER = [
        Equal::class => [0 => 'isEmpty'],
        Greater::class => [0 => 'isNotEmpty'],
        GreaterOrEqual::class => [1 => 'isNotEmpty'],
        Identical::class => [0 => 'isEmpty'],
        NotEqual::class => [0 => 'isNotEmpty'],
        NotIdentical::class => [0 => 'isNotEmpty'],
        Smaller::class => [1 => 'isEmpty'],
        SmallerOrEqual::class => [0 => 'isEmpty'],
    ];

    /**
     * Operator to use when the number is on the left, e.g. `0 < $collection->count()` reads as `count() > 0`.
     *
     * @var array<class-string<BinaryOp>, class-string<BinaryOp>>
     */
    private const array MIRRORED_OPERATORS = [
        Equal::class => Equal::class,
        Greater::class => Smaller::class,
        GreaterOrEqual::class => SmallerOrEqual::class,
        Identical::class => Identical::class,
        NotEqual::class => NotEqual::class,
        NotIdentical::class => NotIdentical::class,
        Smaller::class => Greater::class,
        SmallerOrEqual::class => GreaterOrEqual::class,
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert collection `count()` comparisons against 0 or 1 to `isEmpty()` or `isNotEmpty()`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([1, 2, 3]);
$collection->count() > 0;
$collection->count() === 0;
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([1, 2, 3]);
$collection->isNotEmpty();
$collection->isEmpty();
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
        return array_keys(self::METHOD_BY_OPERATOR_AND_NUMBER);
    }

    /**
     * @param  BinaryOp  $node
     */
    public function refactor(Node $node): ?MethodCall
    {
        if ($node->right instanceof Int_) {
            $operator = $node::class;
            $countCall = $node->left;
            $number = $node->right->value;
        } elseif ($node->left instanceof Int_) {
            $operator = self::MIRRORED_OPERATORS[$node::class];
            $countCall = $node->right;
            $number = $node->left->value;
        } else {
            return null;
        }

        $method = self::METHOD_BY_OPERATOR_AND_NUMBER[$operator][$number] ?? null;
        if ($method === null || ! $countCall instanceof MethodCall || ! $this->isCollectionCountCall($countCall)) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($countCall->var, $method);
    }

    private function isCollectionCountCall(MethodCall $methodCall): bool
    {
        return $this->isName($methodCall->name, 'count')
            && $methodCall->args === []
            && $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Support\Enumerable'));
    }
}

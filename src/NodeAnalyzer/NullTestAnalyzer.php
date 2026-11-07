<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\BinaryOp\LogicalXor;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\While_;
use Rector\NodeNameResolver\NodeNameResolver;
use RectorLaravel\ValueObject\NullTestedExpressions;

use function array_filter;
use function array_values;
use function in_array;
use function spl_object_id;
use function strtolower;

/**
 * Finds the expressions whose null-ness is observed, so that a call returning `?T` is not swapped
 * for one that never returns null.
 */
final readonly class NullTestAnalyzer
{
    /**
     * Assertions and functions that read the truthiness of an argument. Any other call that is
     * given a `null` literal (`assertSame(null, $x)`) is a null test too.
     *
     * @var list<string>
     */
    private const array NULL_SENSITIVE_CALLS = [
        'assertnull',
        'assertnotnull',
        'assertempty',
        'assertnotempty',
        'asserttrue',
        'assertfalse',
        'is_null',
    ];

    /**
     * Pest expectations that read the null-ness or the truthiness of the `expect()` subject. Any
     * other expectation given a `null` literal (`->toBe(null)`) is a null test too.
     *
     * @var list<string>
     */
    private const array PEST_NULL_EXPECTATIONS = [
        'tobenull',
        'tobeempty',
        'tobefalsy',
        'tobetruthy',
        'tobefalse',
        'tobetrue',
    ];

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
    ) {}

    /**
     * @param  array<Node>  $nodes  every node of one scope
     */
    public function analyze(array $nodes): NullTestedExpressions
    {
        $exprIds = [];
        foreach ($nodes as $node) {
            foreach ($this->testedOperands($node) as $operand) {
                foreach ($this->withAssignedValues($operand) as $tested) {
                    $exprIds[spl_object_id($tested)] = true;
                }
            }
        }

        return new NullTestedExpressions($exprIds);
    }

    /**
     * @return array<Expr>
     */
    private function testedOperands(Node $node): array
    {
        return match (true) {
            $node instanceof Identical,
            $node instanceof NotIdentical,
            $node instanceof Equal,
            $node instanceof NotEqual => $this->operandsComparedToNull($node->left, $node->right),
            $node instanceof Coalesce => $this->withReceiverChain($node->left),
            $node instanceof NullsafeMethodCall,
            $node instanceof NullsafePropertyFetch => [$node->var],
            $node instanceof Empty_ => $this->withReceiverChain($node->expr),
            $node instanceof BooleanNot,
            $node instanceof Bool_,
            $node instanceof Instanceof_ => [$node->expr],
            $node instanceof Isset_ => $this->withReceiverChains($node->vars),
            $node instanceof Ternary,
            $node instanceof Switch_,
            $node instanceof Match_,
            $node instanceof If_,
            $node instanceof ElseIf_,
            $node instanceof While_,
            $node instanceof Do_ => [$node->cond],
            $node instanceof For_ => $node->cond,
            $node instanceof BooleanAnd,
            $node instanceof BooleanOr,
            $node instanceof LogicalAnd,
            $node instanceof LogicalOr,
            $node instanceof LogicalXor => [$node->left, $node->right],
            $node instanceof Return_ => $node->expr instanceof Expr ? [$node->expr] : [],
            $node instanceof MethodCall => [...$this->testedArguments($node), ...$this->pestExpectSubjects($node)],
            $node instanceof StaticCall,
            $node instanceof FuncCall => $this->testedArguments($node),
            default => [],
        };
    }

    /**
     * `if (! $fresh = $model->fresh())` tests the value that was assigned, not the assignment.
     *
     * @return list<Expr>
     */
    private function withAssignedValues(Expr $expr): array
    {
        $values = [$expr];
        while ($expr instanceof Assign) {
            $expr = $expr->expr;
            $values[] = $expr;
        }

        return $values;
    }

    /**
     * `isset($model->fresh()->status)`, `empty(...)` and `??` are false for a null receiver anywhere
     * in the chain, so every link is a null test.
     *
     * @param  array<Expr>  $exprs
     * @return list<Expr>
     */
    private function withReceiverChains(array $exprs): array
    {
        $chain = [];
        foreach ($exprs as $expr) {
            foreach ($this->withReceiverChain($expr) as $link) {
                $chain[] = $link;
            }
        }

        return $chain;
    }

    /**
     * @return list<Expr>
     */
    private function withReceiverChain(Expr $expr): array
    {
        $chain = [$expr];
        while ($expr instanceof PropertyFetch
            || $expr instanceof NullsafePropertyFetch
            || $expr instanceof MethodCall
            || $expr instanceof NullsafeMethodCall
            || $expr instanceof ArrayDimFetch) {
            $expr = $expr->var;
            $chain[] = $expr;
        }

        return $chain;
    }

    /**
     * @return list<Expr>
     */
    private function operandsComparedToNull(Expr $left, Expr $right): array
    {
        return match (true) {
            $this->isNullConstant($left) => [$right],
            $this->isNullConstant($right) => [$left],
            default => [],
        };
    }

    /**
     * Assertions name what they check (`assertNull()`) or spell the expected value out as a
     * literal (`assertSame(null, $x)`): both make every other argument a null test.
     *
     * @return list<Expr>
     */
    private function testedArguments(MethodCall|StaticCall|FuncCall $call): array
    {
        $values = $this->argumentValues($call);

        $name = $this->nodeNameResolver->getName($call->name);
        $isNullSensitiveCall = $name !== null && in_array(strtolower($name), self::NULL_SENSITIVE_CALLS, true);

        $hasNullLiteral = $this->hasNullConstant($values);
        if (! $isNullSensitiveCall && ! $hasNullLiteral) {
            return [];
        }

        return array_values(array_filter($values, fn (Expr $value): bool => ! $this->isNullConstant($value)));
    }

    /**
     * `expect($model->fresh())->toBeNull()`, `->not->toBeNull()`, `->toBe(null)`: the subject of
     * the expectation chain is the tested value.
     *
     * @return list<Expr>
     */
    private function pestExpectSubjects(MethodCall $expectation): array
    {
        $name = $this->nodeNameResolver->getName($expectation->name);
        $isNullExpectation = ($name !== null && in_array(strtolower($name), self::PEST_NULL_EXPECTATIONS, true))
            || $this->hasNullConstant($this->argumentValues($expectation));
        if (! $isNullExpectation) {
            return [];
        }

        $root = $expectation;
        while ($root instanceof MethodCall || $root instanceof PropertyFetch) {
            $root = $root->var;
        }

        if (! $root instanceof FuncCall || ! $this->nodeNameResolver->isName($root->name, 'expect')) {
            return [];
        }

        return $this->argumentValues($root);
    }

    /**
     * @return list<Expr>
     */
    private function argumentValues(MethodCall|StaticCall|FuncCall $call): array
    {
        $values = [];
        foreach ($call->args as $arg) {
            if ($arg instanceof Arg) {
                $values[] = $arg->value;
            }
        }

        return $values;
    }

    /**
     * @param  list<Expr>  $values
     */
    private function hasNullConstant(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->isNullConstant($value)) {
                return true;
            }
        }

        return false;
    }

    private function isNullConstant(Expr $expr): bool
    {
        return $expr instanceof ConstFetch && $this->nodeNameResolver->isName($expr->name, 'null');
    }
}

<?php

namespace RectorLaravel\Rector\If_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeVisitor;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\BetterNodeFinder;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\If_\ThrowIfRector\ThrowIfRectorTest
 */
class ThrowIfRector extends AbstractRector
{
    /**
     * @readonly
     */
    private BetterNodeFinder $betterNodeFinder;
    public function __construct(BetterNodeFinder $betterNodeFinder)
    {
        $this->betterNodeFinder = $betterNodeFinder;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change if throw to throw_if', [
            new CodeSample(
                <<<'CODE_SAMPLE'
if ($condition) {
    throw new Exception();
}
if (!$condition) {
    throw new Exception();
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
throw_if($condition, new Exception());
throw_unless($condition, new Exception());
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [If_::class];
    }

    /**
     * @param  If_  $node
     */
    public function refactor(Node $node): ?Node
    {
        /**
         * This is too conservative, can work with cases with elseif and else execution branches.
         * Skip for now for simplicity.
         */
        if ($node->else instanceof Else_ || $node->elseifs !== []) {
            return null;
        }

        $ifStmts = $node->stmts;
        // Check if there's a single throw expression inside the if-statement
        if (! (count($ifStmts) === 1 && $ifStmts[0] instanceof Expression && $ifStmts[0]->expr instanceof Throw_)) {
            return null;
        }

        $ifCondition = $node->cond;
        $throwExpr = $ifStmts[0]->expr;

        if (! $this->isSafeToTransform($throwExpr, $ifCondition)) {
            return null;
        }

        $expression = new Expression(
            $ifCondition instanceof BooleanNot
                ? new FuncCall(new Name('throw_unless'), [new Arg($ifCondition->expr), new Arg($throwExpr->expr)])
                : new FuncCall(new Name('throw_if'), [new Arg($ifCondition), new Arg($throwExpr->expr)])
        );

        $comments = array_merge($node->getComments(), $ifStmts[0]->getComments());
        if ($comments !== []) {
            $expression->setAttribute(AttributeKey::COMMENTS, $comments);
        }

        return $expression;
    }

    private function isSafeToTransform(Throw_ $throw, Expr $expr): bool
    {
        $shouldTransform = true;
        $bannedNodeTypes = [MethodCall::class, StaticCall::class, FuncCall::class, ArrayDimFetch::class, PropertyFetch::class, StaticPropertyFetch::class];
        $this->traverseNodesWithCallable($throw->expr, function (Node $node) use (&$shouldTransform, $bannedNodeTypes, $expr): ?int {
            if (
                in_array(get_class($node), $bannedNodeTypes, true)
                || $node instanceof Variable && ! $this->isSafeToTransformWithVariableAccess($node, $expr)
            ) {
                $shouldTransform = false;

                return NodeVisitor::STOP_TRAVERSAL;
            }

            return null;
        });

        return $shouldTransform;
    }

    /**
     * Not safe to transform when throw expression contains variables that are assigned in the if-condition because of the short-circuit logical operators issue.
     * This method checks if the variable was assigned on the right side of a short-circuit logical operator (conjunction and disjunction).
     * Note: The check is a little too strict, because such a variable may be initialized before the if-statement, and in such case it doesn't matter if it was assigned somewhere in the condition.
     */
    private function isSafeToTransformWithVariableAccess(Variable $variable, Expr $expr): bool
    {
        $firstShortCircuitOperator = $this->betterNodeFinder->findFirst(
            $expr,
            fn (Node $node): bool => $node instanceof BooleanAnd || $node instanceof BooleanOr
        );
        if (! $firstShortCircuitOperator instanceof Node) {
            return true;
        }
        assert($firstShortCircuitOperator instanceof BooleanAnd || $firstShortCircuitOperator instanceof BooleanOr);

        $varName = $this->getName($variable);
        $hasUnsafeAssignment = $this->betterNodeFinder->findFirst(
            $firstShortCircuitOperator->right, // only here short-circuit problem can happen
            fn (Node $node): bool => $node instanceof Assign
                && $node->var instanceof Variable
                && $this->getName($node->var) === $varName
        );

        return ! $hasUnsafeAssignment instanceof Node;
    }
}

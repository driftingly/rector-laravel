<?php

namespace RectorLaravel\Rector\If_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
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
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\If_\ThrowIfRector\ThrowIfRectorTest
 */
class ThrowIfRector extends AbstractRector
{
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

        if ($this->exceptionUsesVariablesAssignedByCondition($throwExpr, $ifCondition)) {
            return null;
        }

        if (! $this->shouldTransform($throwExpr)) {
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

    private function shouldTransform(Throw_ $throwExpr): bool
    {
        $shouldTransform = true;
        $bannedNodeTypes = [MethodCall::class, StaticCall::class, FuncCall::class, ArrayDimFetch::class, PropertyFetch::class, StaticPropertyFetch::class];
        $this->traverseNodesWithCallable($throwExpr->expr, function (Node $node) use (&$shouldTransform, $bannedNodeTypes): ?int {
            if (in_array($node::class, $bannedNodeTypes, true)) {
                $shouldTransform = false;

                return NodeVisitor::STOP_TRAVERSAL;
            }

            return null;
        });

        return $shouldTransform;
    }

    /**
     * Make sure the exception doesn't use variables assigned by the condition or this
     * will cause broken code to be generated
     */
    private function exceptionUsesVariablesAssignedByCondition(Expr $throwExpr, Expr $condition): bool
    {
        $conditionVariables = [];
        $returnValue = false;

        $this->traverseNodesWithCallable($condition, function (Node $node) use (&$conditionVariables): null {
            if ($node instanceof Assign) {
                $conditionVariables[] = $this->getName($node->var);
            }

            return null;
        });

        $this->traverseNodesWithCallable($throwExpr, function (Node $node) use ($conditionVariables, &$returnValue): ?int {
            if ($node instanceof Variable && in_array($this->getName($node), $conditionVariables, true)) {
                $returnValue = true;

                return NodeVisitor::STOP_TRAVERSAL;
            }

            return null;
        });

        return $returnValue;
    }
}

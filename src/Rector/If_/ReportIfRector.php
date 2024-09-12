<?php

namespace RectorLaravel\Rector\If_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeTraverser;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\If_\ReportIfRector\ReportIfRectorTest
 */
class ReportIfRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change if report to report_if', [
            new CodeSample(
                <<<'CODE_SAMPLE'
if ($condition) {
    report(new Exception());
}
if (!$condition) {
    report(new Exception());
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
report_if($condition, new Exception());
report_unless($condition, new Exception());
CODE_SAMPLE
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [If_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof If_) {
            return null;
        }

        $ifStmts = $node->stmts;

        // Check if there's a single statement inside the if block to call abort()
        if (
            count($ifStmts) === 1 &&
            $ifStmts[0] instanceof Expression &&
            $ifStmts[0]->expr instanceof FuncCall &&
            $ifStmts[0]->expr->name instanceof Name &&
            $this->isName($ifStmts[0]->expr, 'report')
        ) {
            $condition = $node->cond;
            /** @var FuncCall $abortCall */
            $abortCall = $ifStmts[0]->expr;

            if ($this->exceptionUsesVariablesAssignedByCondition($abortCall, $condition)) {
                return null;
            }

            // Check if the condition is a negation
            if ($condition instanceof BooleanNot) {
                // Create a new throw_unless function call
                return new Expression(new FuncCall(new Name('report_unless'), array_merge([new Arg($condition->expr)], $abortCall->args)));
            } else {
                // Create a new throw_if function call
                return new Expression(new FuncCall(new Name('report_if'), array_merge([new Arg($condition)], $abortCall->args)));
            }
        }

        return null;
    }

    /**
     * Make sure the exception doesn't use variables assigned by the condition or this
     * will cause broken code to be generated
     */
    private function exceptionUsesVariablesAssignedByCondition(Expr $throwExpr, Expr $condition): bool
    {
        $conditionVariables = [];
        $returnValue = false;

        $this->traverseNodesWithCallable($condition, function (Node $node) use (&$conditionVariables) {
            if ($node instanceof Assign) {
                $conditionVariables[] = $this->getName($node->var);
            }

            return null;
        });

        $this->traverseNodesWithCallable($throwExpr, function (Node $node) use ($conditionVariables, &$returnValue): ?int {
            if ($node instanceof Variable && in_array($this->getName($node), $conditionVariables, true)) {
                $returnValue = true;

                return NodeTraverser::STOP_TRAVERSAL;
            }

            return null;
        });

        return $returnValue;
    }
}

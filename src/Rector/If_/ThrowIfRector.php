<?php

namespace RectorLaravel\Rector\If_;

use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Throw_;
use Rector\Core\Rector\AbstractRector;
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

    public function getNodeTypes(): array
    {
        return [If_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof If_) {
            return null;
        }

        $ifStmts = $node->stmts;

        // Check if there's a single throw statement inside the if
        if (count($ifStmts) === 1 && $ifStmts[0] instanceof Throw_) {
            $condition = $node->cond;
            $throwExpr = $ifStmts[0]->expr;

            // Check if the condition is a negation
            if ($condition instanceof BooleanNot) {
                // Create a new throw_unless function call
                return new Node\Stmt\Expression(new FuncCall(new Node\Name('throw_unless'), [
                    new Node\Arg($condition->expr),
                    new Node\Arg($throwExpr),
                ]));
            } else {
                // Create a new throw_if function call
                return new Node\Stmt\Expression(new FuncCall(new Node\Name('throw_if'), [
                    new Node\Arg($condition),
                    new Node\Arg($throwExpr),
                ]));
            }
        }

        return null;
    }
}

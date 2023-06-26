<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\SleepFuncToSleepStaticCallRector\SleepFuncToSleepStaticCallRectorTest
 */
class SleepFuncToSleepStaticCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Use Sleep::sleep() and Sleep::usleep() instead of the sleep() and usleep() function.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
sleep(5);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
\Illuminate\Support\Sleep::sleep(5);
CODE_SAMPLE,
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $this->isName($node->name, 'sleep') && ! $this->isName($node->name, 'usleep')) {
            return null;
        }

        if ($this->isReturnValueUsed($node)) {
            return null;
        }

        $method = $this->isName($node->name, 'sleep') ? 'sleep' : 'usleep';

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Sleep', $method, $node->args);
    }

    protected function isReturnValueUsed(Node $node): bool
    {
        $parent = $node->getAttribute(AttributeKey::PARENT_NODE);
        if ($parent === null) {
            return false;
        }

        return $parent instanceof Node\Expr\Assign || $parent instanceof Node\Expr\AssignRef ||
            $parent instanceof Node\Expr\BinaryOp || $parent instanceof Node\Expr\BooleanNot ||
            $parent instanceof Node\Expr\Cast || $parent instanceof Node\Expr\Empty_ ||
            $parent instanceof Node\Expr\ErrorSuppress || $parent instanceof Node\Expr\Exit_ ||
            $parent instanceof Node\Expr\Include_ || $parent instanceof Node\Expr\Isset_ ||
            $parent instanceof Node\Expr\List_ || $parent instanceof Node\Expr\Print_ ||
            $parent instanceof Node\Expr\UnaryMinus || $parent instanceof Node\Expr\UnaryPlus ||
            $parent instanceof Node\Expr\Yield_ || $parent instanceof Node\Expr\YieldFrom ||
            $parent instanceof Node\Stmt\Case_ || $parent instanceof Node\Stmt\Else_ ||
            $parent instanceof Node\Stmt\ElseIf_ || $parent instanceof Node\Stmt\For_ ||
            $parent instanceof Node\Stmt\Foreach_ || $parent instanceof Node\Stmt\If_ ||
            $parent instanceof Node\Stmt\Return_ || $parent instanceof Node\Stmt\Switch_ ||
            $parent instanceof Node\Stmt\Throw_ || $parent instanceof Node\Stmt\While_;
    }
}

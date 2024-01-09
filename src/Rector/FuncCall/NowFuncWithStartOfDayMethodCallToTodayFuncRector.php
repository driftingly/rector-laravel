<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\NowFuncWithStartOfDayMethodCallToTodayFuncRector\NowFuncWithStartOfDayMethodCallToTodayFuncRectorTest
 */
class NowFuncWithStartOfDayMethodCallToTodayFuncRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use today() instead of now()->startOfDay()', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$now = now()->startOfDay();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$now = today();
CODE_SAMPLE
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?FuncCall
    {
        if (! $this->isName($node->name, 'startOfDay')) {
            return null;
        }

        if (! $this->isName($node->var, 'now')) {
            return null;
        }

        return $this->nodeFactory->createFuncCall('today');
    }
}

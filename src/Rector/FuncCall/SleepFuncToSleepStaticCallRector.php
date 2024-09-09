<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Expression;
use Rector\Rector\AbstractRector;
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
                new CodeSample(<<<'CODE_SAMPLE'
sleep(5);
CODE_SAMPLE, <<<'CODE_SAMPLE'
\Illuminate\Support\Sleep::sleep(5);
CODE_SAMPLE),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    /**
     * @param  Expression  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->expr instanceof FuncCall) {
            return null;
        }

        if (! $this->isName($node->expr->name, 'sleep') && ! $this->isName($node->expr->name, 'usleep')) {
            return null;
        }

        $method = $this->isName($node->expr->name, 'sleep') ? 'sleep' : 'usleep';

        $node->expr = $this->nodeFactory->createStaticCall('Illuminate\Support\Sleep', $method, $node->expr->args);

        return $node;
    }
}

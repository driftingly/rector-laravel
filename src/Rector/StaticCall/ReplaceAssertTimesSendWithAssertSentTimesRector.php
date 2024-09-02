<?php

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector\ReplaceAssertTimesSendWithAssertSentTimesRectorTest
 */
class ReplaceAssertTimesSendWithAssertSentTimesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace assertTimesSent with assertSentTimes', [
            new CodeSample(<<<'CODE_SAMPLE'
Notification::assertTimesSent(1, SomeNotification::class);
CODE_SAMPLE
, <<<'CODE_SAMPLE'
Notification::assertSentTimes(SomeNotification::class, 1);
CODE_SAMPLE
),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param  StaticCall  $node
     */
    public function refactor(Node $node): ?StaticCall
    {

        if (! $this->isObjectType(
            $node->class,
            new ObjectType('Illuminate\Support\Facades\Notification')
        )) {
            return null;
        }

        if ($this->getName($node->name) !== 'assertTimesSent') {
            return null;
        }

        $node->name = new Identifier('assertSentTimes');

        $node->args = array_reverse($node->args);

        return $node;
    }
}

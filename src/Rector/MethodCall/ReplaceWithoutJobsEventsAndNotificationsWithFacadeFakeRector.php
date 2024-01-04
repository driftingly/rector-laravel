<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRectorTest
 */
class ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace `withoutJobs`, `withoutEvents` and `withoutNotifications` with Facade `fake`',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$this->withoutJobs();
$this->withoutEvents();
$this->withoutNotifications();
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\Bus::fake();
\Illuminate\Support\Facades\Event::fake();
\Illuminate\Support\Facades\Notification::fake();
CODE_SAMPLE,
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  Node\Expr\MethodCall  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $this->isNames($node->name, ['withoutJobs', 'withoutEvents', 'withoutNotifications'])) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Foundation\Testing\TestCase'))) {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        $facade = match ($node->name->name) {
            'withoutJobs' => 'Bus',
            'withoutEvents' => 'Event',
            'withoutNotifications' => 'Notification',
            default => null,
        };

        if ($facade === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Facades\\' . $facade, 'fake');
    }
}

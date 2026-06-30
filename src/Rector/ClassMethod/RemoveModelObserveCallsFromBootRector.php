<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\ObservedByAnalyzer;
use RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\RemoveModelObserveCallsFromBootRectorTest;
use RectorLaravel\ValueObject\ObservedByRegistration;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RemoveModelObserveCallsFromBootRectorTest
 */
final class RemoveModelObserveCallsFromBootRector extends AbstractRector
{
    public function __construct(private readonly ObservedByAnalyzer $observedByAnalyzer) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Removes direct model observe() registrations from boot methods when they can be represented by ObservedBy',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        $this->bootSomethingElse();
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider
{
    public function boot(): void
    {
        $this->bootSomethingElse();
    }
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param  ClassMethod  $node
     */
    public function refactor(Node $node): Node|int|null
    {
        if (! $this->isNames($node->name, ['boot', 'booted'])) {
            return null;
        }

        $currentClassName = $this->observedByAnalyzer->resolveCurrentClassName($node);

        $hasChanged = false;

        foreach ((array) $node->stmts as $key => $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            $expr = $stmt->expr;
            if (! $expr instanceof StaticCall) {
                continue;
            }

            $observedByRegistration = $this->observedByAnalyzer->matchObserveStaticCall($expr, $currentClassName);
            if (! $observedByRegistration instanceof ObservedByRegistration) {
                continue;
            }

            if (! $this->observedByAnalyzer->canUpdateModel($observedByRegistration->modelClass, $observedByRegistration->observerClasses, $this->getFile()->getFilePath())) {
                continue;
            }

            unset($node->stmts[$key]);
            $hasChanged = true;
        }

        if (! $hasChanged) {
            return null;
        }

        $node->stmts = array_values((array) $node->stmts);

        if ($node->stmts === []) {
            return NodeVisitor::REMOVE_NODE;
        }

        return $node;
    }
}

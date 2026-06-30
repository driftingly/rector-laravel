<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\ObservedByAnalyzer;
use RectorLaravel\NodeFactory\ObservedByAttributeFactory;
use RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\ObserveCallsToObservedByAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see ObserveCallsToObservedByAttributeRectorTest
 */
final class ObserveCallsToObservedByAttributeRector extends AbstractRector
{
    public function __construct(
        private readonly ObservedByAnalyzer $observedByAnalyzer,
        private readonly ObservedByAttributeFactory $observedByAttributeFactory,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes manual model observe() registrations in boot methods to the ObservedBy attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
}

class AppServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
}

class AppServiceProvider
{
    public function boot(): void
    {
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
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $className = $this->resolveClassName($node);
        if (! is_string($className)) {
            return null;
        }

        $observerClassesFromObserveCalls = $this->observedByAnalyzer->resolveObserverClassesForModel($className, $this->getFile()->getFilePath());
        if ($observerClassesFromObserveCalls === []) {
            return null;
        }

        if (! $this->observedByAnalyzer->isLikelyEloquentModelClass($node)) {
            return null;
        }

        $existingObservedByClasses = $this->observedByAnalyzer->resolveExistingObservedByClasses($node);
        if ($existingObservedByClasses === null) {
            return null;
        }

        $mergedObserverClasses = array_values(array_unique([
            ...$existingObservedByClasses,
            ...$observerClassesFromObserveCalls,
        ]));

        if ($mergedObserverClasses === $existingObservedByClasses) {
            return null;
        }

        $observedByAttributeGroup = $this->observedByAttributeFactory->create($mergedObserverClasses);
        $matchingAttributeGroupKeys = [];

        foreach ($node->attrGroups as $key => $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($this->isName($attr->name, 'Illuminate\\Database\\Eloquent\\Attributes\\ObservedBy')) {
                    $matchingAttributeGroupKeys[] = $key;
                    break;
                }
            }
        }

        if ($matchingAttributeGroupKeys === []) {
            $node->attrGroups[] = $observedByAttributeGroup;

            return $node;
        }

        $firstMatchingAttributeGroupKey = array_shift($matchingAttributeGroupKeys);
        if (! is_int($firstMatchingAttributeGroupKey)) {
            return null;
        }

        $node->attrGroups[$firstMatchingAttributeGroupKey] = $observedByAttributeGroup;

        foreach ($matchingAttributeGroupKeys as $matchingAttributeGroupKey) {
            unset($node->attrGroups[$matchingAttributeGroupKey]);
        }

        $node->attrGroups = array_values($node->attrGroups);

        return $node;
    }

    private function resolveClassName(Class_ $class): ?string
    {
        if ($class->namespacedName instanceof Name) {
            return $class->namespacedName->toString();
        }

        if (! $class->name instanceof Identifier) {
            return null;
        }

        return $class->name->toString();
    }
}

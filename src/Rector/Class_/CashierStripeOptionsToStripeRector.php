<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractScopeAwareRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/cashier-stripe/blob/master/UPGRADE.md#upgrading-to-130-from-12x
 *
 * @see \RectorLaravel\Tests\Rector\Class_\CashierStripeOptionsToStripeRector\CashierStripeOptionsToStripeRectorTest
 */
final class CashierStripeOptionsToStripeRector extends AbstractScopeAwareRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Renames the Billable stripeOptions() to stripe().', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class User extends Model
{
    use Billable;

    public function stripeOptions(array $options = []) {
        return [];
    }
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class User extends Model
{
    use Billable;

    public function stripe(array $options = []) {
        return [];
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return null;
        }

        if (! $this->usesBillableTrait($scope)) {
            return null;
        }

        /** @var Class_ $node */
        $classMethod = $node->getMethod('stripeOptions');

        if (! $classMethod instanceof ClassMethod) {
            return null;
        }

        $classMethod->name = new Identifier('stripe');

        return $node;
    }

    private function usesBillableTrait(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();

        while ($classReflection instanceof ClassReflection) {
            foreach ($classReflection->getTraits() as $traitReflection) {
                if ($traitReflection->hasTraitUse('Laravel\Cashier\Billable')) {
                    return true;
                }
            }

            $classReflection = $classReflection->getParentClass();
        }

        return true;
    }
}

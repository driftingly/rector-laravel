<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/pull/39310
 *
 * @see \RectorLaravel\Tests\Rector\Class_\RemoveModelPropertyFromFactoriesRector\RemoveModelPropertyFromFactoriesRectorTest
 */
final class RemoveModelPropertyFromFactoriesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Removes the $model property from Factories.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
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

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Factories\Factory'))) {
            return null;
        }

        foreach ($node->stmts as $index => $stmt) {
            if (! $stmt instanceof Property) {
                continue;
            }

            if (! $this->isName($stmt, 'model')) {
                continue;
            }

            unset($node->stmts[$index]);

            break;
        }

        return $node;
    }
}

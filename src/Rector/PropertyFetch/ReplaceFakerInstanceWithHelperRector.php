<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\PropertyFetch;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector\ReplaceFakerInstanceWithHelperRectorTest
 */
final class ReplaceFakerInstanceWithHelperRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace $this->faker with the fake() helper function in Factories',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
        ];
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @param PropertyFetch $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkipNode($node)) {
            return null;
        }

//        // skip $this->faker->randomEnum
//        $previousNode = $this->betterNodeFinder->findFirstPreviousOfTypes($node, [MethodCall::class]);
//
//        if ($previousNode !== null && $this->isName($previousNode->name, 'randomEnum')) {
//            return null;
//        }

        return $this->nodeFactory->createFuncCall('fake');
    }

    private function shouldSkipNode(PropertyFetch $propertyFetch): bool
    {
        if (! $this->nodeNameResolver->isName($propertyFetch->var, 'this')) {
            return true;
        }

        if (! $this->nodeNameResolver->isName($propertyFetch->name, 'faker')) {
            return true;
        }

        $classLike = $this->betterNodeFinder->findParentType($propertyFetch, ClassLike::class);

        if (! $classLike instanceof ClassLike) {
            return true;
        }

        if ($classLike instanceof Class_) {
            return ! $this->isObjectType($classLike, new ObjectType('Illuminate\Database\Eloquent\Factories\Factory'));
        }

        return false;
    }
}

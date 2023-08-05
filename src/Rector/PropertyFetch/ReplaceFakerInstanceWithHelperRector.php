<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\PropertyFetch;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Reflection\ClassReflection;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector\ReplaceFakerInstanceWithHelperRectorTest
 */
final class ReplaceFakerInstanceWithHelperRector extends AbstractRector
{
    public function __construct(
        private readonly ReflectionResolver $reflectionResolver
    ) {
    }

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
        return [PropertyFetch::class, MethodCall::class];
    }

    /**
     * @param PropertyFetch|MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($node);

        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if (! $classReflection->isSubclassOf('Illuminate\Database\Eloquent\Factories\Factory')) {
            return null;
        }

        if ($node instanceof MethodCall) {
            if (! $node->var instanceof PropertyFetch) {
                return null;
            }

            // The randomEnum() method is a special case where the faker instance is used
            // see https://github.com/spatie/laravel-enum#faker-provider
            if ($this->isName($node->name, 'randomEnum')) {
                return null;
            }

            return $this->refactorPropertyFetch($node);
        }

        if ($node->var instanceof PropertyFetch) {
            return $this->refactorPropertyFetch($node);
        }

        return null;
    }

    private function refactorPropertyFetch(MethodCall|PropertyFetch $node): MethodCall|PropertyFetch|null
    {
        if (! $node->var instanceof PropertyFetch) {
            return null;
        }

        $funcCall = $this->getFuncCall($node->var);

        if ($funcCall === null) {
            return null;
        }

        $node->var = $funcCall;
        return $node;
    }

    private function getFuncCall(PropertyFetch $node): ?FuncCall
    {
        if (! $this->isName($node->var, 'this')) {
            return null;
        }

        if (! $this->isName($node->name, 'faker')) {
            return null;
        }

        return $this->nodeFactory->createFuncCall('fake');
    }
}

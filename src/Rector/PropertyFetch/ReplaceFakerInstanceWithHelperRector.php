<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\PropertyFetch;

use Override;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Scalar\InterpolatedString;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Reflection\ReflectionResolver;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector\ReplaceFakerInstanceWithHelperRectorTest
 */
final class ReplaceFakerInstanceWithHelperRector extends AbstractRector
{
    /**
     * @readonly
     */
    private ReflectionResolver $reflectionResolver;
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @var string
     */
    private const IS_IN_RANDOM_ENUM = 'is_in_random_enum';

    public function __construct(ReflectionResolver $reflectionResolver, ReflectionProvider $reflectionProvider)
    {
        $this->reflectionResolver = $reflectionResolver;
        $this->reflectionProvider = $reflectionProvider;
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
        return [PropertyFetch::class, MethodCall::class, InterpolatedString::class];
    }

    #[Override]
    public function beforeTraverse(array $nodes): array
    {
        parent::beforeTraverse($nodes);

        $this->traverseNodesWithCallable($nodes, function (Node $node) {
            if (! $node instanceof MethodCall) {
                return null;
            }

            // The randomEnum() method is a special case where the faker instance is used
            // see https://github.com/spatie/laravel-enum#faker-provider
            if ($this->isName($node->name, 'randomEnum')) {
                $node->setAttribute(self::IS_IN_RANDOM_ENUM, true);
                $this->traverseNodesWithCallable($node, function (Node $subNode) {
                    if (! $subNode instanceof PropertyFetch && ! $subNode instanceof InterpolatedString) {
                        return null;
                    }

                    $subNode->setAttribute(self::IS_IN_RANDOM_ENUM, true);

                    return $subNode;
                });

                return $node;
            }

            return null;
        });

        return $nodes;
    }

    /**
     * @param  PropertyFetch|MethodCall|InterpolatedString  $node
     */
    public function refactor(Node $node): ?Node
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($node);

        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if (! $classReflection->isSubclassOfClass($this->reflectionProvider->getClass('Illuminate\Database\Eloquent\Factories\Factory'))) {
            return null;
        }

        if ($node->getAttribute(self::IS_IN_RANDOM_ENUM) === true) {
            return null;
        }

        if ($node instanceof InterpolatedString) {
            return $this->refactorInterpolatedString($node);
        }

        return $this->refactorFakerReference($node);
    }

    private function refactorInterpolatedString(InterpolatedString $interpolatedString): ?Node
    {
        $hasChanged = false;
        $parts = [];
        $nonFakerParts = [];

        foreach ($interpolatedString->parts as $part) {
            $faker = $this->refactorFakerReference($part);

            if (! $faker instanceof Node) {
                $nonFakerParts[] = $part;

                continue;
            }

            if ($nonFakerParts !== []) {
                $parts[] = new InterpolatedString($nonFakerParts);
                $nonFakerParts = [];
            }

            $parts[] = $faker;
            $hasChanged = true;
        }

        if (! $hasChanged) {
            return null;
        }

        if ($nonFakerParts !== []) {
            $parts[] = new InterpolatedString($nonFakerParts);
        }

        return array_reduce(
            $parts,
            fn (?Expr $carry, Expr $part) => $carry === null ? $part : new Concat($carry, $part),
        );
    }

    private function refactorFakerReference(Node $node): ?Expr
    {
        if (! $node instanceof PropertyFetch && ! $node instanceof MethodCall) {
            return null;
        }

        if (! $node->var instanceof PropertyFetch) {

            return null;
        }

        $funcCall = $this->getFuncCall($node->var);

        if (! $funcCall instanceof FuncCall) {
            return null;
        }

        $node->var = $funcCall;

        return $node;
    }

    private function getFuncCall(PropertyFetch $propertyFetch): ?FuncCall
    {
        if (! $this->isName($propertyFetch->var, 'this')) {
            return null;
        }

        if (! $this->isName($propertyFetch->name, 'faker')) {
            return null;
        }

        return $this->nodeFactory->createFuncCall('fake');
    }
}

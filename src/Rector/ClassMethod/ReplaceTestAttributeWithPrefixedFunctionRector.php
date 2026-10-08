<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\ReplaceTestAttributeWithPrefixedFunctionRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see ReplaceTestAttributeWithPrefixedFunctionRectorTest
 */
final class ReplaceTestAttributeWithPrefixedFunctionRector extends AbstractRector
{
    private const string TEST_ATTRIBUTE = 'PHPUnit\Framework\Attributes\Test';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace the PHPUnit #[Test] attribute with a test prefixed method name',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\Attributes\Test;

class SomeTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    public function it_adds_numbers(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    #[Test]
    public function onePlusOneShouldBeTwo(): void
    {
        $this->assertSame(2, 1 + 1);
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\Attributes\Test;

class SomeTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_adds_numbers(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    public function testOnePlusOneShouldBeTwo(): void
    {
        $this->assertSame(2, 1 + 1);
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
        if (! $this->isObjectType($node, new ObjectType('PHPUnit\Framework\TestCase'))) {
            return null;
        }

        $changes = false;
        foreach ($node->getMethods() as $classMethod) {
            if (! $this->hasTestAttribute($classMethod)) {
                continue;
            }

            $name = $this->getName($classMethod);

            if (! str_starts_with($name, 'test')) {
                $newName = str_contains($name, '_')
                    ? 'test_' . $name
                    : 'test' . ucfirst($name);

                if ($node->getMethod($newName) instanceof ClassMethod) {
                    continue;
                }

                $classMethod->name = new Identifier($newName);
            }

            $this->removeTestAttribute($classMethod);
            $changes = true;
        }

        if ($changes === false) {
            return null;
        }

        return $node;
    }

    private function hasTestAttribute(ClassMethod $classMethod): bool
    {
        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if ($this->isName($attribute->name, self::TEST_ATTRIBUTE)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function removeTestAttribute(ClassMethod $classMethod): void
    {
        foreach ($classMethod->attrGroups as $groupKey => $attrGroup) {
            foreach ($attrGroup->attrs as $attrKey => $attribute) {
                if ($this->isName($attribute->name, self::TEST_ATTRIBUTE)) {
                    unset($attrGroup->attrs[$attrKey]);
                }
            }

            if ($attrGroup->attrs === []) {
                unset($classMethod->attrGroups[$groupKey]);

                continue;
            }

            $attrGroup->attrs = array_values($attrGroup->attrs);
        }

        $classMethod->attrGroups = array_values($classMethod->attrGroups);
    }
}

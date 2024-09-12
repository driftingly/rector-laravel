<?php

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\LivewireComponentComputedMethodToComputedAttributeRectorTest
 */
final class LivewireComponentComputedMethodToComputedAttributeRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer
     */
    private $phpAttributeAnalyzer;
    private const COMPUTED_ATTRIBUTE = 'Livewire\Attributes\Computed';

    private const COMPONENT_CLASS = 'Livewire\Component';

    private const METHOD_PATTERN = '/^get(?\'methodName\'[\w]*)Property$/';

    public function __construct(PhpAttributeAnalyzer $phpAttributeAnalyzer)
    {
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Converts the computed methods of a Livewire component to use the Computed Attribute',
            [
                new CodeSample(<<<'CODE_SAMPLE'
use Livewire\Component;

class MyComponent extends Component
{
    public function getFooBarProperty()
    {
    }
}
CODE_SAMPLE
,
                    <<<'CODE_SAMPLE'
use Livewire\Component;

class MyComponent extends Component
{
    #[\Livewire\Attributes\Computed]
    public function fooBar()
    {
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Class_
    {
        if (! $this->isObjectType($node, new ObjectType(self::COMPONENT_CLASS))) {
            return null;
        }

        $changes = false;

        foreach ($node->stmts as $stmt) {
            if (
                $stmt instanceof ClassMethod &&
                $stmt->isPublic() &&
                (bool) preg_match(self::METHOD_PATTERN, $this->getName($stmt), $matches)) {
                $methodName = lcfirst($matches['methodName']);

                if ($this->methodExistsInClass($node, $methodName)) {
                    continue;
                }

                $this->addComputedAttributeToClassMethodAndRename($stmt, $methodName);
                $changes = true;
            }
        }

        if ($changes === false) {
            return null;
        }

        return $node;
    }

    private function addComputedAttributeToClassMethodAndRename(ClassMethod $classMethod, string $name): void
    {
        if ($this->phpAttributeAnalyzer->hasPhpAttribute($classMethod, self::COMPUTED_ATTRIBUTE)) {
            return;
        }

        $classMethod->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified(self::COMPUTED_ATTRIBUTE)
            ),
        ]);

        $classMethod->name = new Identifier($name);
    }

    private function methodExistsInClass(Class_ $class, string $methodName): bool
    {
        return $this->getType($class)->hasMethod($methodName)->yes();
    }
}

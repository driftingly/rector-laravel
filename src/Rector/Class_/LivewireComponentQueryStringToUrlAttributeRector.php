<?php

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\LivewireComponentQueryStringToUrlAttributeRectorTest
 */
final class LivewireComponentQueryStringToUrlAttributeRector extends AbstractRector
{
    private const URL_ATTRIBUTE = 'Livewire\Attributes\Url';
    private const COMPONENT_CLASS = 'Livewire\Component';
    private const QUERY_STRING_PROPERTY_NAME = 'queryString';

    public function __construct(private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer)
    {

    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Converts the $queryString property of a Livewire component to use the Url Attribute',
            [
                new CodeSample(<<<'CODE_SAMPLE'
use Livewire\Component;

class MyComponent extends Component
{
    public string $something = '';

    public string $another = '';

    protected $queryString = [
        'something',
        'another',
    ];
}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
use Livewire\Component;

class MyComponent extends Component
{
    #[\Livewire\Attributes\Url]
    public string $something = '';

    #[\Livewire\Attributes\Url]
    public string $another = '';
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

        $queryStringProperty = null;

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Property && $this->isName($stmt, self::QUERY_STRING_PROPERTY_NAME)) {
                $queryStringProperty = $stmt;
            }
        }

        if (! $queryStringProperty instanceof Property) {
            return null;
        }

        // find the properties and add the attribute
        $urlPropertyNames = $this->findQueryStringProperties($queryStringProperty);

        if ($urlPropertyNames === []) {
            return null;
        }

        $propertyNodes = [];

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Property && $this->isNames($stmt, $urlPropertyNames)) {
                $propertyNodes[] = $stmt;
            }
        }

        // only apply the url if all named properties in the queryString property have been resolved
        if (count($propertyNodes) < count($urlPropertyNames)) {
            return null;
        }

        foreach ($propertyNodes as $propertyNode) {
            $this->addUrlAttributeToProperty($propertyNode);
        }

        // remove the query string property
        $node->stmts = array_filter($node->stmts, fn (Node $node) => $node !== $queryStringProperty);

        return $node;
    }

    /**
     * @return string[]
     */
    private function findQueryStringProperties(Property $property): array
    {
        if ($property->props === []) {
            return [];
        }

        $array = $property->props[0]->default;

        if (! $array instanceof Array_ || $array->items === []) {
            return [];
        }

        $properties = [];

        foreach ($array->items as $item) {
            if ($item === null) {
                continue;
            }

            if ($item->value instanceof String_) {
                $properties[] = $item->value->value;
            }
        }

        if (count($properties) !== count($array->items)) {
            return [];
        }

        return $properties;
    }

    private function addUrlAttributeToProperty(Property $property): void
    {
        if ($this->phpAttributeAnalyzer->hasPhpAttribute($property, self::URL_ATTRIBUTE)) {
            return;
        }

        $property->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified(self::URL_ATTRIBUTE))]);
    }
}

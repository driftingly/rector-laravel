<?php

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar;
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
    /**
     * @readonly
     * @var \Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer
     */
    private $phpAttributeAnalyzer;
    private const URL_ATTRIBUTE = 'Livewire\Attributes\Url';

    private const COMPONENT_CLASS = 'Livewire\Component';

    private const QUERY_STRING_PROPERTY_NAME = 'queryString';

    public function __construct(PhpAttributeAnalyzer $phpAttributeAnalyzer)
    {
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
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
CODE_SAMPLE
,
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
            if ($stmt instanceof Property && $this->isNames($stmt, array_keys((array) $urlPropertyNames))) {
                $propertyNodes[] = $stmt;
            }
        }

        foreach ($propertyNodes as $propertyNode) {
            $args = $urlPropertyNames[$this->getName($propertyNode)] ?? [];
            $this->addUrlAttributeToProperty($propertyNode, $args);
        }

        // remove the query string property if now empty
        $this->attemptQueryStringRemoval($node, $queryStringProperty);

        return $node;
    }

    /**
     * @return array<string, Node\Arg[]>|null
     */
    private function findQueryStringProperties(Property $property): ?array
    {
        if ($property->props === []) {
            return null;
        }

        $array = $property->props[0]->default;

        if (! $array instanceof Array_ || $array->items === []) {
            return null;
        }

        $properties = [];
        $toFilter = [];

        foreach ($array->items as $item) {
            if ($item === null) {
                continue;
            }

            if ($item->key instanceof String_ && $item->value instanceof Array_) {
                $args = $this->processArrayOptionsIntoArgs($item->value);

                if ($args === null) {
                    continue;
                }

                $properties[$item->key->value] = $args;
                $toFilter[] = $item;

                continue;
            }

            if ($item->value instanceof String_) {
                $properties[$item->value->value] = [];
                $toFilter[] = $item;
            }
        }

        if ($properties === []) {
            return null;
        }

        // we remove the array properties which will be converted
        $array->items = array_filter($array->items, function (?ArrayItem $arrayItem) use ($toFilter) : bool {
            return ! in_array($arrayItem, $toFilter, true);
        });

        return $properties;
    }

    /**
     * @param  Node\Arg[]  $args
     */
    private function addUrlAttributeToProperty(Property $property, array $args): void
    {
        if ($this->phpAttributeAnalyzer->hasPhpAttribute($property, self::URL_ATTRIBUTE)) {
            return;
        }

        $property->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified(self::URL_ATTRIBUTE), $args
            ),
        ]);
    }

    /**
     * @return Node\Arg[]|null
     */
    private function processArrayOptionsIntoArgs(Array_ $array): ?array
    {
        $args = [];

        foreach ($array->items as $item) {
            if ($item === null) {
                continue;
            }
            if ($item->key instanceof String_ && $item->value instanceof Scalar && in_array($item->key->value, ['except', 'as'], true)) {
                $args[] = new Arg($item->value, false, false, [], new Identifier($item->key->value));
            }
        }

        if (count($args) !== count($array->items)) {
            return null;
        }

        return $args;
    }

    private function attemptQueryStringRemoval(Class_ $class, Property $property): void
    {
        $array = $property->props[0]->default;

        if ($array instanceof Array_ && $array->items === []) {
            $class->stmts = array_filter($class->stmts, function (Node $node) use ($property) {
                return $node !== $property;
            });
        }
    }
}

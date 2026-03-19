<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeFactory\TableAttributeFactory;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\TablePropertyToTableAttributeRectorTest
 */
final class TablePropertyToTableAttributeRector extends AbstractRector
{
    /**
     * @readonly
     */
    private TableAttributeFactory $tableAttributeFactory;
    /**
     * @readonly
     */
    private PhpAttributeAnalyzer $phpAttributeAnalyzer;
    public function __construct(TableAttributeFactory $tableAttributeFactory, PhpAttributeAnalyzer $phpAttributeAnalyzer)
    {
        $this->tableAttributeFactory = $tableAttributeFactory;
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model table-related properties to use the Table attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $keyType = 'string';

    protected $incrementing = false;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table(table: 'users', key: 'user_id', keyType: 'string', incrementing: false)]
class User extends Model
{
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
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return null;
        }

        $tableAttributeClass = 'Illuminate\Database\Eloquent\Attributes\Table';
        $hasExistingAttribute = $this->phpAttributeAnalyzer->hasPhpAttribute($node, $tableAttributeClass);

        // Resolve the table value: from $table property, or from existing attribute
        $tableProperty = $node->getProperty('table');
        $tableValue = null;

        if ($tableProperty !== null) {
            if (! $tableProperty->isProtected()) {
                return null;
            }
            $tableValue = $this->getPropertyDefaultValue($tableProperty);
            if (! $tableValue instanceof String_) {
                return null;
            }
        }

        if (! $tableValue instanceof String_ && $hasExistingAttribute) {
            $tableValue = $this->getExistingAttributeArg($node, $tableAttributeClass, 'table');
            if (! $tableValue instanceof String_) {
                return null;
            }
        }

        if (! $tableValue instanceof String_) {
            return null;
        }

        // Seed options from existing attribute args (properties take precedence below)
        $options = [];
        if ($hasExistingAttribute) {
            foreach (['key', 'keyType', 'incrementing'] as $argName) {
                $existingArg = $this->getExistingAttributeArg($node, $tableAttributeClass, $argName);
                if ($existingArg instanceof Expr) {
                    $options[$argName] = $existingArg;
                }
            }
        }

        $primaryKeyProperty = $node->getProperty('primaryKey');
        if ($primaryKeyProperty !== null && $primaryKeyProperty->isProtected()) {
            $primaryKeyValue = $this->getPropertyDefaultValue($primaryKeyProperty);
            if ($primaryKeyValue instanceof Expr) {
                $options['key'] = $primaryKeyValue;
            } else {
                $primaryKeyProperty = null;
            }
        } else {
            $primaryKeyProperty = null;
        }

        $keyTypeProperty = $node->getProperty('keyType');
        if ($keyTypeProperty !== null && $keyTypeProperty->isProtected()) {
            $keyTypeValue = $this->getPropertyDefaultValue($keyTypeProperty);
            if ($keyTypeValue instanceof Expr) {
                $options['keyType'] = $keyTypeValue;
            } else {
                $keyTypeProperty = null;
            }
        } else {
            $keyTypeProperty = null;
        }

        $incrementingProperty = $node->getProperty('incrementing');
        if ($incrementingProperty !== null && $incrementingProperty->isProtected()) {
            $incrementingValue = $this->getPropertyDefaultValue($incrementingProperty);
            if ($incrementingValue instanceof Expr) {
                $options['incrementing'] = $incrementingValue;
            } else {
                $incrementingProperty = null;
            }
        } else {
            $incrementingProperty = null;
        }

        // If the attribute exists but there are no properties to remove, there is nothing to do
        if ($hasExistingAttribute && $tableProperty === null && $primaryKeyProperty === null && $keyTypeProperty === null && $incrementingProperty === null) {
            return null;
        }

        $attributeGroup = $this->tableAttributeFactory->create($tableValue, $options);

        if ($hasExistingAttribute) {
            foreach ($node->attrGroups as $key => $attrGroup) {
                foreach ($attrGroup->attrs as $attr) {
                    if ($this->isName($attr->name, $tableAttributeClass)) {
                        $node->attrGroups[$key] = $attributeGroup;
                        break 2;
                    }
                }
            }
        } else {
            $node->attrGroups[] = $attributeGroup;
        }

        // Remove properties
        if ($tableProperty !== null) {
            $this->removeProperty($node, $tableProperty);
        }
        if ($primaryKeyProperty !== null) {
            $this->removeProperty($node, $primaryKeyProperty);
        }
        if ($keyTypeProperty !== null) {
            $this->removeProperty($node, $keyTypeProperty);
        }
        if ($incrementingProperty !== null) {
            $this->removeProperty($node, $incrementingProperty);
        }

        return $node;
    }

    private function getPropertyDefaultValue(Property $property): ?Expr
    {
        return $property->props[0]->default;
    }

    private function getExistingAttributeArg(Class_ $class, string $attributeClass, string $argName): ?Expr
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($this->isName($attr->name, $attributeClass)) {
                    foreach ($attr->args as $arg) {
                        if ($arg->name !== null && $arg->name->name === $argName) {
                            return $arg->value;
                        }
                    }
                }
            }
        }

        return null;
    }

    private function removeProperty(Class_ $class, Property $property): void
    {
        foreach ($class->stmts as $key => $stmt) {
            if ($stmt === $property) {
                unset($class->stmts[$key]);
                break;
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeFactory\TableAttributeFactory;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\TablePropertyToTableAttributeRectorTest
 */
final class TablePropertyToTableAttributeRector extends AbstractRector
{
    public function __construct(
        private readonly TableAttributeFactory $tableAttributeFactory,
    ) {}

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

        $tableProperty = $node->getProperty('table');
        if ($tableProperty === null || ! $tableProperty->isProtected()) {
            return null;
        }

        $tableValue = $this->getPropertyDefaultValue($tableProperty);
        if (! $tableValue instanceof String_) {
            return null;
        }

        $options = [];

        $primaryKeyProperty = $node->getProperty('primaryKey');
        if ($primaryKeyProperty !== null && $primaryKeyProperty->isProtected()) {
            $primaryKeyValue = $this->getPropertyDefaultValue($primaryKeyProperty);
            if ($primaryKeyValue instanceof Expr) {
                $options['key'] = $primaryKeyValue;
            }
        }

        $keyTypeProperty = $node->getProperty('keyType');
        if ($keyTypeProperty !== null && $keyTypeProperty->isProtected()) {
            $keyTypeValue = $this->getPropertyDefaultValue($keyTypeProperty);
            if ($keyTypeValue instanceof Expr) {
                $options['keyType'] = $keyTypeValue;
            }
        }

        $incrementingProperty = $node->getProperty('incrementing');
        if ($incrementingProperty !== null && $incrementingProperty->isProtected()) {
            $incrementingValue = $this->getPropertyDefaultValue($incrementingProperty);
            if ($incrementingValue instanceof Expr) {
                $options['incrementing'] = $incrementingValue;
            }
        }

        $node->attrGroups[] = $this->tableAttributeFactory->create($tableValue, $options);

        // Remove properties
        $this->removeProperty($node, $tableProperty);
        if (isset($primaryKeyProperty)) {
            $this->removeProperty($node, $primaryKeyProperty);
        }
        if (isset($keyTypeProperty)) {
            $this->removeProperty($node, $keyTypeProperty);
        }
        if (isset($incrementingProperty)) {
            $this->removeProperty($node, $incrementingProperty);
        }

        return $node;
    }

    private function getPropertyDefaultValue(Property $property): ?Expr
    {
        return $property->props[0]->default;
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

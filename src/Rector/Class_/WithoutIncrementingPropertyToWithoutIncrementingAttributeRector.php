<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector\WithoutIncrementingPropertyToWithoutIncrementingAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see WithoutIncrementingPropertyToWithoutIncrementingAttributeRectorTest
 */
final class WithoutIncrementingPropertyToWithoutIncrementingAttributeRector extends AbstractRector
{
    /**
     * @readonly
     */
    private PhpAttributeAnalyzer $phpAttributeAnalyzer;
    public function __construct(PhpAttributeAnalyzer $phpAttributeAnalyzer)
    {
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model incrementing = false property to use the WithoutIncrementing attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $incrementing = false;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;

#[WithoutIncrementing]
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

        $incrementingProperty = $node->getProperty('incrementing');
        if ($incrementingProperty === null) {
            return null;
        }

        if (! $incrementingProperty->isPublic()) {
            return null;
        }

        $propertyProperty = $incrementingProperty->props[0];
        if ($propertyProperty->default === null) {
            return null;
        }

        $value = $propertyProperty->default;
        if (! $value instanceof ConstFetch || $value->name->toLowerString() !== 'false') {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\WithoutIncrementing')) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified('Illuminate\Database\Eloquent\Attributes\WithoutIncrementing')),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $incrementingProperty) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }
}

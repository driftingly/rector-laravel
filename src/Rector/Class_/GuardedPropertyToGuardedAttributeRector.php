<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\GuardedPropertyToGuardedAttributeRectorTest
 */
final class GuardedPropertyToGuardedAttributeRector extends AbstractRector
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
            'Changes model guarded property to use the guarded attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [
        'is_admin',
    ];
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Guarded;

#[Guarded(['is_admin'])]
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

        $guardedProperty = $node->getProperty('guarded');
        if ($guardedProperty === null) {
            return null;
        }

        if (! $guardedProperty->isProtected()) {
            return null;
        }

        $propertyProperty = $guardedProperty->props[0];
        if ($propertyProperty->default === null || ! $propertyProperty->default instanceof Array_) {
            return null;
        }

        $guardedArray = $propertyProperty->default;

        if ($guardedArray->items === []) {
            return null;
        }

        if (! $this->isArrayOfStrings($guardedArray)) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\Guarded')) {
            return null;
        }

        // Add attribute to class
        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Database\Eloquent\Attributes\Guarded'),
                [new Arg($guardedArray)]
            ),
        ]);

        // Remove property
        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $guardedProperty) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }

    private function isArrayOfStrings(Array_ $array): bool
    {
        foreach ($array->items as $item) {
            if ($item === null) {
                return false;
            }

            if (! $item->value instanceof String_) {
                return false;
            }
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector\EmptyGuardedPropertyToUnguardedAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see EmptyGuardedPropertyToUnguardedAttributeRectorTest
 */
final class EmptyGuardedPropertyToUnguardedAttributeRector extends AbstractRector implements ComposerPackageConstraintInterface
{
    public function __construct(private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer) {}

    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=13.0');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model empty guarded property to use the Unguarded attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Unguarded;

#[Unguarded]
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

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\Unguarded')) {
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

        if ($propertyProperty->default->items !== []) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified('Illuminate\Database\Eloquent\Attributes\Unguarded')),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $guardedProperty) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }
}

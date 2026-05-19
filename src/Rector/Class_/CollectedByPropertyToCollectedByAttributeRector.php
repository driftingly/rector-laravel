<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\CollectedByPropertyToCollectedByAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see CollectedByPropertyToCollectedByAttributeRectorTest
 */
final class CollectedByPropertyToCollectedByAttributeRector extends AbstractRector
{
    public function __construct(private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model collectionClass property to use the CollectedBy attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use App\Collections\UserCollection;

class User extends Model
{
    protected $collectionClass = UserCollection::class;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use App\Collections\UserCollection;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;

#[CollectedBy(UserCollection::class)]
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

        $collectionClassProperty = $node->getProperty('collectionClass');
        if ($collectionClassProperty === null) {
            return null;
        }

        if (! $collectionClassProperty->isProtected()) {
            return null;
        }

        $propertyProperty = $collectionClassProperty->props[0];
        if ($propertyProperty->default === null) {
            return null;
        }

        $value = $propertyProperty->default;
        if (! $value instanceof ClassConstFetch && ! $value instanceof String_) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\CollectedBy')) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Database\Eloquent\Attributes\CollectedBy'),
                [new Arg($value)]
            ),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $collectionClassProperty) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }
}

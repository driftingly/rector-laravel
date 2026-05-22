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
use RectorLaravel\Tests\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector\PreserveKeysPropertyToPreserveKeysAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see PreserveKeysPropertyToPreserveKeysAttributeRectorTest
 */
final class PreserveKeysPropertyToPreserveKeysAttributeRector extends AbstractRector
{
    public function __construct(
        private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes the preserveKeys property to use the PreserveKeys attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $preserveKeys = true;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Http\Resources\Attributes\PreserveKeys;
use Illuminate\Http\Resources\Json\JsonResource;

#[PreserveKeys]
class UserResource extends JsonResource
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
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Http\Resources\Json\JsonResource'))) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Http\Resources\Attributes\PreserveKeys')) {
            return null;
        }

        $property = $node->getProperty('preserveKeys');
        if ($property === null) {
            return null;
        }

        if (! $property->isProtected()) {
            return null;
        }

        $propertyProperty = $property->props[0];
        if ($propertyProperty->default === null) {
            return null;
        }

        $value = $propertyProperty->default;
        if (! $this->getType($value)->isTrue()->yes() && (! $value instanceof ConstFetch || $value->name->toLowerString() !== 'true')) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified('Illuminate\Http\Resources\Attributes\PreserveKeys')),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $property) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }
}

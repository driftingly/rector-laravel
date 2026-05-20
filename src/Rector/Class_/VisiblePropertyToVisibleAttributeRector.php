<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\VisiblePropertyToVisibleAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see VisiblePropertyToVisibleAttributeRectorTest
 */
final class VisiblePropertyToVisibleAttributeRector extends AbstractRector
{
    public function __construct(private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model visible property to use the Visible attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $visible = [
        'name',
        'email',
    ];
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Visible;

#[Visible(['name', 'email'])]
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

        $visibleProperty = $node->getProperty('visible');
        if ($visibleProperty === null) {
            return null;
        }

        if (! $visibleProperty->isProtected()) {
            return null;
        }

        $propertyProperty = $visibleProperty->props[0];
        if ($propertyProperty->default === null || ! $propertyProperty->default instanceof Array_) {
            return null;
        }

        $visibleArray = $propertyProperty->default;

        if ($visibleArray->items === []) {
            return null;
        }

        if (! $this->isArrayOfStrings($visibleArray)) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\Visible')) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Database\Eloquent\Attributes\Visible'),
                [new Arg($visibleArray)]
            ),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $visibleProperty) {
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

            if (! $this->getType($item->value)->isString()->yes()) {
                return false;
            }
        }

        return true;
    }
}

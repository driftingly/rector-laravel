<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\ErrorBagPropertyToErrorBagAttributeRector\ErrorBagPropertyToErrorBagAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see ErrorBagPropertyToErrorBagAttributeRectorTest
 */
final class ErrorBagPropertyToErrorBagAttributeRector extends AbstractRector
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
            'Changes the errorBag property to use the ErrorBag attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    protected $errorBag = 'custom';
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Attributes\ErrorBag;

#[ErrorBag('custom')]
class StorePostRequest extends FormRequest
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
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Foundation\Http\FormRequest'))) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Foundation\Http\Attributes\ErrorBag')) {
            return null;
        }

        $property = $node->getProperty('errorBag');
        if ($property === null) {
            return null;
        }

        if (! $property->isProtected()) {
            return null;
        }

        $propertyProperty = $property->props[0];
        if ($propertyProperty->default === null || ! $propertyProperty->default instanceof String_) {
            return null;
        }

        $value = $propertyProperty->default;

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Foundation\Http\Attributes\ErrorBag'),
                [new Arg($value)]
            ),
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

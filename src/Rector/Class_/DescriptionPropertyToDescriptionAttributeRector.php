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
use RectorLaravel\Tests\Rector\Class_\DescriptionPropertyToDescriptionAttributeRector\DescriptionPropertyToDescriptionAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see DescriptionPropertyToDescriptionAttributeRectorTest
 */
final class DescriptionPropertyToDescriptionAttributeRector extends AbstractRector
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
            'Changes the description property to use the Description attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $description = 'Send marketing emails to users';
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;

#[Description('Send marketing emails to users')]
class SendEmails extends Command
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
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Console\Command'))) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Console\Attributes\Description')) {
            return null;
        }

        $property = $node->getProperty('description');
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
                new FullyQualified('Illuminate\Console\Attributes\Description'),
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

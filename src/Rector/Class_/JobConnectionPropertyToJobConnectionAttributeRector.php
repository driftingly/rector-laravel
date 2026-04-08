<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector\JobConnectionPropertyToJobConnectionAttributeRectorTest
 */
final class JobConnectionPropertyToJobConnectionAttributeRector extends AbstractRector
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
            'Changes the connection property to use the Connection attribute on queue jobs',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Contracts\Queue\ShouldQueue;

final class ProcessPodcast implements ShouldQueue
{
    public $connection = 'redis';
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\Connection;

#[Connection('redis')]
final class ProcessPodcast implements ShouldQueue
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
        if (! $node->isFinal()) {
            return null;
        }

        if (! $this->isObjectType($node, new ObjectType('Illuminate\Contracts\Queue\ShouldQueue'))) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Queue\Attributes\Connection')) {
            return null;
        }

        $property = $node->getProperty('connection');
        if ($property === null) {
            return null;
        }

        if (! $property->isPublic()) {
            return null;
        }

        $propertyProperty = $property->props[0];
        if ($propertyProperty->default === null) {
            return null;
        }

        $value = $propertyProperty->default;

        // Add attribute to class
        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Queue\Attributes\Connection'),
                [new Arg($value)]
            ),
        ]);

        // Remove property
        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $property) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }
}

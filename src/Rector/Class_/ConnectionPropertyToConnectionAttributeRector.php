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
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\ConnectionPropertyToConnectionAttributeRector\ConnectionPropertyToConnectionAttributeRectorTest
 */
final class ConnectionPropertyToConnectionAttributeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model connection property to use the Connection attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'sqlite';
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Connection;

#[Connection('sqlite')]
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

        $connectionProperty = $node->getProperty('connection');
        if ($connectionProperty === null) {
            return null;
        }

        if (! $connectionProperty->isProtected()) {
            return null;
        }

        $propertyProperty = $connectionProperty->props[0];
        if ($propertyProperty->default === null || ! $propertyProperty->default instanceof String_) {
            return null;
        }

        $connectionValue = $propertyProperty->default;

        // Add attribute to class
        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Database\Eloquent\Attributes\Connection'),
                [new Arg($connectionValue)]
            ),
        ]);

        // Remove property
        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $connectionProperty) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }
}

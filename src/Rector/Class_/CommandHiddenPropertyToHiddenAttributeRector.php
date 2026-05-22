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
use RectorLaravel\Tests\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector\CommandHiddenPropertyToHiddenAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see CommandHiddenPropertyToHiddenAttributeRectorTest
 */
final class CommandHiddenPropertyToHiddenAttributeRector extends AbstractRector
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
            'Changes the hidden property to use the Hidden attribute on console commands',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $hidden = true;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Hidden;

#[Hidden]
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

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Console\Attributes\Hidden')) {
            return null;
        }

        $property = $node->getProperty('hidden');
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
            new Attribute(new FullyQualified('Illuminate\Console\Attributes\Hidden')),
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

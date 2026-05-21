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
use RectorLaravel\Tests\Rector\Class_\HelpPropertyToHelpAttributeRector\HelpPropertyToHelpAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see HelpPropertyToHelpAttributeRectorTest
 */
final class HelpPropertyToHelpAttributeRector extends AbstractRector
{
    public function __construct(
        private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes the help property to use the Help attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $help = 'This command sends emails to all users';
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Help;

#[Help('This command sends emails to all users')]
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

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Console\Attributes\Help')) {
            return null;
        }

        $property = $node->getProperty('help');
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
                new FullyQualified('Illuminate\Console\Attributes\Help'),
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

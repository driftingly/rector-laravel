<?php

declare(strict_types=1);

namespace __NAMESPACE__;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \__TESTS_NAMESPACE__\__NAME__\__NAME__Test
 */
final class __NAME__ extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes something',
            [new CodeSample(
                <<<'CODE_SAMPLE'
before
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
after
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        // @todo select node type
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        // @todo change the node

        return $node;
    }
}

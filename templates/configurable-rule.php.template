<?php

declare(strict_types=1);

namespace __NAMESPACE__;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use RectorLaravel\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \__TESTS_NAMESPACE__\__NAME__\__NAME__Test
 */
final class __NAME__ extends AbstractRector implements ConfigurableRectorInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes something',
            [new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
before
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
after
CODE_SAMPLE
            , 
            ['option' => 'value']
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
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        // Add configuration logic here
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
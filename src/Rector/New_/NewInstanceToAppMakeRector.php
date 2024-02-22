<?php

namespace RectorLaravel\Rector\New_;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\New_\NewInstanceToAppMakeRector\NewInstanceToAppMakeRectorTest
 */
class NewInstanceToAppMakeRector extends AbstractRector implements ConfigurableRectorInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change new instance to a fetch class via the container', [
            new CodeSample(
                <<<'CODE_SAMPLE'
new SomeClass();
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\App::make(SomeClass::class);
CODE_SAMPLE,
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param  New_  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $node->class instanceof Name) {
            return null;
        }

        return $this->nodeFactory->createStaticCall(
            'Illuminate\Support\Facades\App',
            'make',
            $this->nodeFactory->createArgs(
                [
                    $this->nodeFactory->createClassConstReference($node->class->toString()),
                ]
            )
        );
    }

    public function configure(array $configuration): void
    {
        Assert::allString($configuration);


    }
}

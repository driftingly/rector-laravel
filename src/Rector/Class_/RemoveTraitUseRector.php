<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\RemoveTraitUseRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see RemoveTraitUseRectorTest
 */
final class RemoveTraitUseRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string[]
     */
    private array $traitsToRemove = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove specific traits from code', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeController
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesResources;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeController
{
}
CODE_SAMPLE
                ,
                ['Illuminate\Foundation\Auth\Access\AuthorizesResources']
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    /**
     * @param  Class_|Trait_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;

        foreach ($node->stmts as $key => $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $traitKey => $trait) {
                if (! $this->isNames($trait, $this->traitsToRemove)) {
                    continue;
                }

                unset($stmt->traits[$traitKey]);
                $hasChanged = true;
            }

            // remove empty trait uses
            if ($stmt->traits === []) {
                unset($node->stmts[$key]);
            }
        }

        if (! $hasChanged) {
            return null;
        }

        return $node;
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allString($configuration);
        $this->traitsToRemove = $configuration;
    }
}

<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\FuncCall\FuncCallToNewRector\FuncCallToNewRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see FuncCallToNewRectorTest
 */
final class FuncCallToNewRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, string>
     */
    private array $functionsToNew = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change configured function calls to new Instance', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $collection = collect([]);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $collection = new \Illuminate\Support\Collection([]);
    }
}
CODE_SAMPLE
                ,
                ['collect' => 'Illuminate\Support\Collection']
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        $funcName = $this->getName($node);
        if ($funcName === null) {
            return null;
        }

        $className = $this->functionsToNew[strtolower($funcName)] ?? null;
        if ($className === null) {
            return null;
        }

        return new New_(new FullyQualified($className), $node->getArgs());
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allString(array_keys($configuration));
        Assert::allString($configuration);

        // keyed by lowercased function name, as function names are case-insensitive
        $this->functionsToNew = [];
        foreach ($configuration as $function => $className) {
            $this->functionsToNew[strtolower($function)] = $className;
        }
    }
}

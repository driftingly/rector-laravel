<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\FuncCall\FuncCallToStaticCallRector\FuncCallToStaticCallRectorTest;
use RectorLaravel\ValueObject\FuncCallToStaticCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see FuncCallToStaticCallRectorTest
 */
final class FuncCallToStaticCallRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, FuncCallToStaticCall>
     */
    private array $funcCallsToStaticCalls = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Turn defined function call to static method call', [
            new ConfiguredCodeSample(
                'array_get($array, "key");',
                '\Illuminate\Support\Arr::get($array, "key");',
                [new FuncCallToStaticCall('array_get', 'Illuminate\Support\Arr', 'get')]
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

        $funcCallToStaticCall = $this->funcCallsToStaticCalls[strtolower($funcName)] ?? null;
        if (! $funcCallToStaticCall instanceof FuncCallToStaticCall) {
            return null;
        }

        return $this->nodeFactory->createStaticCall(
            $funcCallToStaticCall->getNewClassName(),
            $funcCallToStaticCall->getNewMethodName(),
            $node->getArgs()
        );
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, FuncCallToStaticCall::class);

        // keyed by lowercased function name, as function names are case-insensitive
        $this->funcCallsToStaticCalls = [];
        foreach ($configuration as $funcCallToStaticCall) {
            $this->funcCallsToStaticCalls[strtolower($funcCallToStaticCall->getOldFuncName())] ??= $funcCallToStaticCall;
        }
    }
}

<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Exception\ShouldNotHappenException;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\FuncCallStaticCallToMethodCallAnalyzer;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\StaticCallToMethodCallRectorTest;
use RectorLaravel\ValueObject\StaticCallToMethodCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see StaticCallToMethodCallRectorTest
 */
final class StaticCallToMethodCallRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var StaticCallToMethodCall[]
     */
    private array $staticCallsToMethodCalls = [];

    public function __construct(
        private readonly FuncCallStaticCallToMethodCallAnalyzer $funcCallStaticCallToMethodCallAnalyzer
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change static call to service method via constructor injection', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\Response;

class SomeController
{
    public function action()
    {
        return Response::view('example', ['new_example' => 123]);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\Response;

class SomeController
{
    public function __construct(private \Illuminate\Contracts\Routing\ResponseFactory $responseFactory)
    {
    }

    public function action()
    {
        return $this->responseFactory->view('example', ['new_example' => 123]);
    }
}
CODE_SAMPLE
                ,
                [
                    new StaticCallToMethodCall(
                        'Illuminate\Support\Facades\Response',
                        '*',
                        'Illuminate\Contracts\Routing\ResponseFactory',
                        '*'
                    ),
                ]
            ),
        ]);
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
        $class = $node;
        $hasChanged = false;

        foreach ($node->getMethods() as $classMethod) {
            $this->traverseNodesWithCallable($classMethod, function (Node $node) use (
                $class,
                $classMethod,
                &$hasChanged
            ): null|MethodCall|StaticCall {
                if (! $node instanceof StaticCall) {
                    return null;
                }

                foreach ($this->staticCallsToMethodCalls as $staticCallToMethodCall) {
                    if (! $staticCallToMethodCall->isStaticCallMatch($node)) {
                        continue;
                    }

                    if ($classMethod->isStatic()) {
                        $hasChanged = true;

                        return $this->refactorToInstanceCall($node, $staticCallToMethodCall);
                    }

                    $expr = $this->funcCallStaticCallToMethodCallAnalyzer->matchTypeProvidingExpr(
                        $class,
                        $classMethod,
                        $staticCallToMethodCall->getClassObjectType(),
                    );

                    if ($expr === null) {
                        return null;
                    }

                    $methodName = $this->getMethodName($node, $staticCallToMethodCall);

                    $hasChanged = true;

                    return new MethodCall($expr, $methodName, $node->args);
                }

                return $node;
            });
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, StaticCallToMethodCall::class);

        $this->staticCallsToMethodCalls = $configuration;
    }

    private function getMethodName(
        StaticCall $staticCall,
        StaticCallToMethodCall $staticCallToMethodCall
    ): string {
        if ($staticCallToMethodCall->getMethodName() === '*') {
            $methodName = $this->getName($staticCall->name);
        } else {
            $methodName = $staticCallToMethodCall->getMethodName();
        }

        if (! is_string($methodName)) {
            throw new ShouldNotHappenException;
        }

        return $methodName;
    }

    private function refactorToInstanceCall(
        StaticCall $staticCall,
        StaticCallToMethodCall $staticCallToMethodCall
    ): MethodCall {
        $new = new New_(new FullyQualified($staticCallToMethodCall->getClassType()));

        $methodName = $this->getMethodName($staticCall, $staticCallToMethodCall);

        return new MethodCall($new, $methodName, $staticCall->args);
    }
}

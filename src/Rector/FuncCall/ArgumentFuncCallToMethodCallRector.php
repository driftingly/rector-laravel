<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\TypeAnalyzer\ArrayTypeAnalyzer;
use Rector\Transform\NodeAnalyzer\FuncCallStaticCallToMethodCallAnalyzer;
use RectorLaravel\Contract\ValueObject\ArgumentFuncCallToMethodCallInterface;
use RectorLaravel\ValueObject\ArgumentFuncCallToMethodCall;
use RectorLaravel\ValueObject\ArrayFuncCallToMethodCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\ArgumentFuncCallToMethodCallRectorTest
 */
final class ArgumentFuncCallToMethodCallRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ArgumentFuncCallToMethodCallInterface[]
     */
    private array $argumentFuncCallToMethodCalls = [];

    public function __construct(
        private readonly ArrayTypeAnalyzer $arrayTypeAnalyzer,
        private readonly FuncCallStaticCallToMethodCallAnalyzer $funcCallStaticCallToMethodCallAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Move help facade-like function calls to constructor injection', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeController
{
    public function action()
    {
        $template = view('template.blade');
        $viewFactory = view();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeController
{
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    private $viewFactory;

    public function __construct(\Illuminate\Contracts\View\Factory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    public function action()
    {
        $template = $this->viewFactory->make('template.blade');
        $viewFactory = $this->viewFactory;
    }
}
CODE_SAMPLE
                ,
                [new ArgumentFuncCallToMethodCall('view', 'Illuminate\Contracts\View\Factory', 'make')]
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
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;
        $class = $node;

        foreach ($node->getMethods() as $classMethod) {
            if ($classMethod->isStatic()) {
                continue;
            }

            if ($classMethod->isAbstract()) {
                continue;
            }

            $this->traverseNodesWithCallable($classMethod, function (Node $node) use (
                $class,
                $classMethod,
                &$hasChanged
            ): ?Node {
                if (! $node instanceof FuncCall) {
                    return null;
                }

                foreach ($this->argumentFuncCallToMethodCalls as $argumentFuncCallToMethodCall) {
                    if (! $this->isName($node->name, $argumentFuncCallToMethodCall->getFunction())) {
                        continue;
                    }

                    if ($argumentFuncCallToMethodCall instanceof ArgumentFuncCallToMethodCall) {
                        $expr = $this->funcCallStaticCallToMethodCallAnalyzer->matchTypeProvidingExpr(
                            $class,
                            $classMethod,
                            new ObjectType($argumentFuncCallToMethodCall->getClass()),
                        );

                        $hasChanged = true;

                        return $this->refactorFuncCallToMethodCall(
                            $node,
                            $argumentFuncCallToMethodCall,
                            $expr
                        );
                    }

                    if ($argumentFuncCallToMethodCall instanceof ArrayFuncCallToMethodCall) {
                        $expr = $this->funcCallStaticCallToMethodCallAnalyzer->matchTypeProvidingExpr(
                            $class,
                            $classMethod,
                            new ObjectType($argumentFuncCallToMethodCall->getClass()),
                        );

                        $hasChanged = true;

                        return $this->refactorArrayFunctionToMethodCall(
                            $node,
                            $argumentFuncCallToMethodCall,
                            $expr
                        );
                    }
                }

                return null;
            });
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsInstanceOf($configuration, ArgumentFuncCallToMethodCallInterface::class);

        $this->argumentFuncCallToMethodCalls = $configuration;
    }

    function refactorFuncCallToMethodCall(
        FuncCall $node,
        ArgumentFuncCallToMethodCall $argumentFuncCallToMethodCall,
        MethodCall|PropertyFetch|Variable $expr
    ): MethodCall|PropertyFetch|Variable {
        if ($argumentFuncCallToMethodCall->getMethodIfArgs() === null) {
            return $this->refactorEmptyFuncCallArgs($argumentFuncCallToMethodCall, $expr);
        }

        return $this->nodeFactory->createMethodCall(
            $expr,
            $argumentFuncCallToMethodCall->getMethodIfArgs(),
            $node->args
        );
    }

    private function refactorArrayFunctionToMethodCall(
        FuncCall $funcCall,
        ArrayFuncCallToMethodCall $arrayFuncCallToMethodCall,
        MethodCall|PropertyFetch|Variable $expr
    ): ?Node {
        if ($funcCall->getArgs() === []) {
            return $expr;
        }

        if ($this->arrayTypeAnalyzer->isArrayType($funcCall->getArgs()[0]->value)) {
            return new MethodCall($expr, $arrayFuncCallToMethodCall->getArrayMethod(), $funcCall->getArgs());
        }

        if ($arrayFuncCallToMethodCall->getNonArrayMethod() === '') {
            return null;
        }

        return new MethodCall($expr, $arrayFuncCallToMethodCall->getNonArrayMethod(), $funcCall->getArgs());
    }

    private function refactorEmptyFuncCallArgs(
        ArgumentFuncCallToMethodCall $argumentFuncCallToMethodCall,
        MethodCall|PropertyFetch|Variable $expr
    ): MethodCall | PropertyFetch | Variable {
        if ($argumentFuncCallToMethodCall->getMethodIfNoArgs() !== null) {
            return $this->nodeFactory->createMethodCall(
                $expr,
                $argumentFuncCallToMethodCall->getMethodIfNoArgs()
            );
        }

        return $expr;
    }
}

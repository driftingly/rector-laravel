<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Rector\Naming\Naming\PropertyNaming;
use Rector\Naming\ValueObject\ExpectedName;
use Rector\NodeTypeResolver\TypeAnalyzer\ArrayTypeAnalyzer;
use Rector\PostRector\Collector\PropertyToAddCollector;
use Rector\PostRector\ValueObject\PropertyMetadata;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
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
        private readonly PropertyNaming $propertyNaming,
        private readonly PropertyToAddCollector $propertyToAddCollector
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
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkipFuncCall($node)) {
            return null;
        }

        /** @var Class_ $classLike */
        $classLike = $this->betterNodeFinder->findParentType($node, Class_::class);

        foreach ($this->argumentFuncCallToMethodCalls as $argumentFuncCallToMethodCall) {
            if (! $this->isName($node, $argumentFuncCallToMethodCall->getFunction())) {
                continue;
            }

            if ($argumentFuncCallToMethodCall instanceof ArgumentFuncCallToMethodCall) {
                return $this->refactorFuncCallToMethodCall($argumentFuncCallToMethodCall, $classLike, $node);
            }

            if ($argumentFuncCallToMethodCall instanceof ArrayFuncCallToMethodCall) {
                return $this->refactorArrayFunctionToMethodCall($argumentFuncCallToMethodCall, $node, $classLike);
            }
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

    private function shouldSkipFuncCall(FuncCall $funcCall): bool
    {
        // we can inject only in injectable class method  context
        /** @var ClassMethod|null $classMethod */
        $classMethod = $this->betterNodeFinder->findParentType($funcCall, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return true;
        }

        return $classMethod->isStatic();
    }

    /**
     * @return MethodCall|PropertyFetch|null
     */
    private function refactorFuncCallToMethodCall(
        ArgumentFuncCallToMethodCall $argumentFuncCallToMethodCall,
        Class_ $class,
        FuncCall $funcCall
    ): ?Node {
        $fullyQualifiedObjectType = new FullyQualifiedObjectType($argumentFuncCallToMethodCall->getClass());
        $expectedName = $this->propertyNaming->getExpectedNameFromType($fullyQualifiedObjectType);

        if (! $expectedName instanceof ExpectedName) {
            throw new ShouldNotHappenException();
        }

        $propertyMetadata = new PropertyMetadata(
            $expectedName->getName(),
            $fullyQualifiedObjectType,
            Class_::MODIFIER_PRIVATE
        );
        $this->propertyToAddCollector->addPropertyToClass($class, $propertyMetadata);

        $propertyFetchNode = $this->nodeFactory->createPropertyFetch('this', $expectedName->getName());

        if ($funcCall->args === []) {
            return $this->refactorEmptyFuncCallArgs($argumentFuncCallToMethodCall, $propertyFetchNode);
        }

        if ($this->isFunctionToMethodCallWithArgs($funcCall, $argumentFuncCallToMethodCall)) {
            $methodName = $argumentFuncCallToMethodCall->getMethodIfArgs();
            if (! is_string($methodName)) {
                throw new ShouldNotHappenException();
            }

            return new MethodCall($propertyFetchNode, $methodName, $funcCall->args);
        }

        return null;
    }

    /**
     * @return PropertyFetch|MethodCall|null
     */
    private function refactorArrayFunctionToMethodCall(
        ArrayFuncCallToMethodCall $arrayFuncCallToMethodCall,
        FuncCall $funcCall,
        Class_ $class
    ): ?Node {
        $propertyName = $this->propertyNaming->fqnToVariableName($arrayFuncCallToMethodCall->getClass());
        $propertyFetch = $this->nodeFactory->createPropertyFetch('this', $propertyName);

        $fullyQualifiedObjectType = new FullyQualifiedObjectType($arrayFuncCallToMethodCall->getClass());

        $propertyMetadata = new PropertyMetadata($propertyName, $fullyQualifiedObjectType, Class_::MODIFIER_PRIVATE);
        $this->propertyToAddCollector->addPropertyToClass($class, $propertyMetadata);

        return $this->createMethodCallArrayFunctionToMethodCall(
            $funcCall,
            $arrayFuncCallToMethodCall,
            $propertyFetch
        );
    }

    private function refactorEmptyFuncCallArgs(
        ArgumentFuncCallToMethodCall $argumentFuncCallToMethodCall,
        PropertyFetch $propertyFetch
    ): MethodCall | PropertyFetch {
        if ($argumentFuncCallToMethodCall->getMethodIfNoArgs() !== null) {
            $methodName = $argumentFuncCallToMethodCall->getMethodIfNoArgs();
            return new MethodCall($propertyFetch, $methodName);
        }

        return $propertyFetch;
    }

    private function isFunctionToMethodCallWithArgs(
        FuncCall $funcCall,
        ArgumentFuncCallToMethodCall $argumentFuncCallToMethodCall
    ): bool {
        if ($argumentFuncCallToMethodCall->getMethodIfArgs() === null) {
            return false;
        }

        return count($funcCall->args) >= 1;
    }

    /**
     * @return PropertyFetch|MethodCall|null
     */
    private function createMethodCallArrayFunctionToMethodCall(
        FuncCall $funcCall,
        ArrayFuncCallToMethodCall $arrayFuncCallToMethodCall,
        PropertyFetch $propertyFetch
    ): ?Node {
        if ($funcCall->getArgs() === []) {
            return $propertyFetch;
        }

        if ($this->arrayTypeAnalyzer->isArrayType($funcCall->getArgs()[0]->value)) {
            return new MethodCall($propertyFetch, $arrayFuncCallToMethodCall->getArrayMethod(), $funcCall->getArgs());
        }

        if ($arrayFuncCallToMethodCall->getNonArrayMethod() === '') {
            return null;
        }

        return new MethodCall($propertyFetch, $arrayFuncCallToMethodCall->getNonArrayMethod(), $funcCall->getArgs());
    }
}

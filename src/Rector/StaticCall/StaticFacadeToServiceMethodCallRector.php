<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Exception\ShouldNotHappenException;
use Rector\Naming\Naming\PropertyNaming;
use Rector\NodeManipulator\ClassDependencyManipulator;
use Rector\PostRector\ValueObject\PropertyMetadata;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\StaticFacadeToServiceMethodCallRectorTest;
use RectorLaravel\ValueObject\StaticCallToMethodCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see StaticFacadeToServiceMethodCallRectorTest
 */
final class StaticFacadeToServiceMethodCallRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var StaticCallToMethodCall[]
     */
    private array $staticCallsToMethodCalls = [];

    public function __construct(
        private readonly ClassDependencyManipulator $classDependencyManipulator,
        private readonly PropertyNaming $propertyNaming
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change static facade call to service method via constructor injection', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeController
{
    public function store()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeController
{
    public function __construct(private readonly \Illuminate\Auth\AuthManager $authManager)
    {
    }
    public function store()
    {
        return $this->authManager->user();
    }
}
CODE_SAMPLE
                ,
                [new StaticCallToMethodCall('Illuminate\Support\Facades\Auth', '*', 'Illuminate\Auth\AuthManager', '*')]
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
            ): ?Node {
                if (! $node instanceof StaticCall) {
                    return null;
                }

                foreach ($this->staticCallsToMethodCalls as $staticCallToMethodCall) {
                    if (! $staticCallToMethodCall->isStaticCallMatch($node)) {
                        continue;
                    }

                    $hasChanged = true;

                    if ($classMethod->isStatic()) {
                        return $this->refactorToInstanceCall($node, $staticCallToMethodCall);
                    }

                    $propertyFetch = $this->resolvePropertyFetch($class, $staticCallToMethodCall->getClassType());

                    return new MethodCall($propertyFetch, $this->resolveMethodName($node, $staticCallToMethodCall), $node->args);
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
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, StaticCallToMethodCall::class);

        $this->staticCallsToMethodCalls = $configuration;
    }

    private function refactorToInstanceCall(
        StaticCall $staticCall,
        StaticCallToMethodCall $staticCallToMethodCall
    ): MethodCall {
        $new = new New_(new FullyQualified($staticCallToMethodCall->getClassType()));

        return new MethodCall($new, $this->resolveMethodName($staticCall, $staticCallToMethodCall), $staticCall->args);
    }

    private function resolveMethodName(StaticCall $staticCall, StaticCallToMethodCall $staticCallToMethodCall): string
    {
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

    private function resolvePropertyFetch(Class_ $class, string $className): PropertyFetch
    {
        $objectType = new ObjectType($className);
        $propertyName = $this->propertyNaming->fqnToVariableName($objectType);
        $this->classDependencyManipulator->addConstructorDependency($class, new PropertyMetadata($propertyName, $objectType));

        return new PropertyFetch(new Variable('this'), $propertyName);
    }
}

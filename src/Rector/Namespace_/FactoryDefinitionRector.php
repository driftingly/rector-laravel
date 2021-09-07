<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\Namespace_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use Rector\Core\PhpParser\Node\CustomNode\FileWithoutNamespace;
use Rector\Core\Rector\AbstractRector;
use Rector\Laravel\NodeFactory\ModelFactoryFactory;
use Symplify\Astral\ValueObject\NodeBuilder\PropertyBuilder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Laravel\Tests\Rector\Namespace_\FactoryDefinitionRector\FactoryDefinitionRectorTest
 */
class FactoryDefinitionRector extends AbstractRector
{
    public function __construct(
        private ModelFactoryFactory $modelFactoryFactory
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change app() func calls to facade calls', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return app('translator')->trans('value');
    }
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return \Illuminate\Support\Facades\App::get('translator')->trans('value');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Namespace_::class, FileWithoutNamespace::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Namespace_|FileWithoutNamespace $node
     */
    public function refactor(Node $node): ?Node
    {
        $factories = [];
        foreach ($node->stmts as $key => $stmt) {
            if (! $stmt instanceof Node\Stmt\Expression) {
                continue;
            }

            if ($this->factoryTypeChanged($stmt->expr)) {
                return null;
            }
            if (! $stmt->expr instanceof Node\Expr\MethodCall) {
                continue;
            }
            if ($this->shouldSkipExpression($stmt->expr)) {
                continue;
            }
            $name = $this->getNameFromClassConstFetch($stmt->expr->args[0]->value);
            if ($name === null) {
                continue;
            }
            if (! isset($factories[$name->toString()])) {
                $factories[$name->toString()] = $this->createFactory($name->getLast(), $stmt->expr->args[0]->value);
            }
            $this->processFactoryConfiguration($factories[$name->toString()], $stmt->expr);

            unset($node->stmts[$key]);
        }
        foreach ($factories as $factory) {
            $node->stmts[] = $factory;
        }

        return $node;
    }

    public function addState(Node\Stmt\Class_ $factory, Node\Expr\MethodCall $methodCall): void
    {
        if (count($methodCall->args) !== 3) {
            return;
        }

        $factory->stmts[] = $this->modelFactoryFactory->createStateMethod($methodCall);
    }

    public function addDefinition(Node\Stmt\Class_ $factory, Node\Expr\MethodCall $methodCall): void
    {
        if (count($methodCall->args) !== 2) {
            return;
        }
        $callback = $methodCall->args[1]->value;
        if (! $callback instanceof Node\Expr\Closure) {
            return;
        }
        $factory->stmts[] = $this->modelFactoryFactory->createDefinition($callback);
    }

    private function getNameFromClassConstFetch(Expr $classConstFetch): ?Node\Name
    {
        if (! $classConstFetch instanceof Node\Expr\ClassConstFetch) {
            return null;
        }
        if ($classConstFetch->class instanceof Expr) {
            return null;
        }
        return $classConstFetch->class;
    }

    private function processFactoryConfiguration(Node\Stmt\Class_ $factory, Node\Expr\MethodCall $methodCall): void
    {
        if ($this->isName($methodCall->name, 'define')) {
            $this->addDefinition($factory, $methodCall);
        }

        if ($this->isName($methodCall->name, 'state')) {
            $this->addState($factory, $methodCall);
        }

        if (! $this->isNames($methodCall->name, ['afterMaking', 'afterCreating'])) {
            return;
        }
        $name = $this->getName($methodCall->name);
        if ($name === null) {
            return;
        }
        $this->addAfterCalling($factory, $methodCall, $name);
    }

    private function shouldSkipExpression(Node\Expr\MethodCall $methodCall): bool
    {
        if (! $methodCall->args[0]->value instanceof Node\Expr\ClassConstFetch) {
            return true;
        }
        if (! $this->isNames($methodCall->name, ['define', 'state', 'afterMaking', 'afterCreating'])) {
            return true;
        }
        return false;
    }

    private function factoryTypeChanged(Node\Expr $expr): bool
    {
        if (! $expr instanceof Node\Expr\Assign) {
            return false;
        }

        return $this->isName($expr->var, 'factory');
    }

    private function createFactory(string $name, Expr $expr): Node\Stmt\Class_
    {
        $factory = new Node\Stmt\Class_($name . 'Factory');
        $factory->extends = new Node\Name\FullyQualified('Illuminate\Database\Eloquent\Factories\Factory');
        $builder = new PropertyBuilder('model');
        $builder->makeProtected();
        $builder->setDefault($expr);
        $model = $builder->getNode();
        $factory->stmts[] = $model;
        return $factory;
    }

    private function addAfterCalling(Node\Stmt\Class_ $factory, Node\Expr\MethodCall $methodCall, string $name): void
    {
        if (count($methodCall->args) !== 2) {
            return;
        }
        $method = $factory->getMethod('configure');
        if ($method === null) {
            $method = $this->modelFactoryFactory->createEmptyConfigure();
            $factory->stmts[] = $method;
        }
        $closure = $methodCall->args[1]->value;
        if (! $closure instanceof Node\Expr\Closure) {
            return;
        }
        $this->modelFactoryFactory->appendConfigure($method, $name, $closure);
    }

    /**
     * @param Node\Stmt[] $stmts
     * @param \PhpParser\Node\Param $param
     */
    private function fakerVariableToPropertyFetch(array $stmts, Node\Param $param): void
    {
        $this->traverseNodesWithCallable($stmts, function (Node $node) use ($param) {
            if (! $node instanceof Node\Expr\Variable) {
                return null;
            }
            $name = $this->getName($param->var);
            if ($name === null) {
                return null;
            }
            if (! $this->isName($node, $name)) {
                return null;
            }
            return $this->nodeFactory->createPropertyFetch('this', 'faker');
        });
    }
}

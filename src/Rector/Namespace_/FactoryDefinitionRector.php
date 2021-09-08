<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\Namespace_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use Rector\Core\PhpParser\Node\CustomNode\FileWithoutNamespace;
use Rector\Core\Rector\AbstractRector;
use Rector\Laravel\NodeFactory\ModelFactoryFactory;
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
        return new RuleDefinition('Upgrade legacy factories to support classes.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
    ];
});
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
use Faker\Generator as Faker;

class UserFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = App\User::class;
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ];
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

    private function factoryTypeChanged(Node\Expr $expr): bool
    {
        if (! $expr instanceof Node\Expr\Assign) {
            return false;
        }

        return $this->isName($expr->var, 'factory');
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

    private function createFactory(string $name, Expr $expr): Node\Stmt\Class_
    {
        return $this->modelFactoryFactory->createEmptyFactory($name, $expr);
    }

    private function processFactoryConfiguration(Node\Stmt\Class_ $factory, Node\Expr\MethodCall $methodCall): void
    {
        if (! $this->isName($methodCall->var, 'factory')) {
            return;
        }
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

    private function addDefinition(Node\Stmt\Class_ $factory, Node\Expr\MethodCall $methodCall): void
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

    private function addState(Node\Stmt\Class_ $factory, Node\Expr\MethodCall $methodCall): void
    {
        if (count($methodCall->args) !== 3) {
            return;
        }

        $factory->stmts[] = $this->modelFactoryFactory->createStateMethod($methodCall);
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
}

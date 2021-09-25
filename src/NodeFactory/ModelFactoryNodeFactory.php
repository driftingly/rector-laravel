<?php

declare(strict_types=1);

namespace Rector\Laravel\NodeFactory;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\NodeAnalyzer\ArgsAnalyzer;
use Rector\Core\PhpParser\Node\NodeFactory;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;
use Symplify\Astral\ValueObject\NodeBuilder\MethodBuilder;
use Symplify\Astral\ValueObject\NodeBuilder\PropertyBuilder;

final class ModelFactoryNodeFactory
{
    /**
     * @var string
     */
    private const THIS = 'this';

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeFactory $nodeFactory,
        private ValueResolver $valueResolver,
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser,
        private ArgsAnalyzer $argsAnalyzer
    ) {
    }

    public function createEmptyFactory(string $name, Expr $expr): Class_
    {
        $class = new Class_($name . 'Factory');
        $class->extends = new FullyQualified('Illuminate\Database\Eloquent\Factories\Factory');
        $propertyBuilder = new PropertyBuilder('model');
        $propertyBuilder->makeProtected();
        $propertyBuilder->setDefault($expr);

        $property = $propertyBuilder->getNode();
        $class->stmts[] = $property;
        return $class;
    }

    public function createDefinition(Closure $closure): ClassMethod
    {
        if (isset($closure->params[0])) {
            $this->fakerVariableToPropertyFetch($closure->stmts, $closure->params[0]);
        }

        return $this->createPublicMethod('definition', $closure->stmts);
    }

    public function createStateMethod(MethodCall $methodCall): ?ClassMethod
    {
        if (! $this->argsAnalyzer->isArgInstanceInArgsPosition($methodCall->args, 2)) {
            return null;
        }

        /** @var Arg $thirdArg */
        $thirdArg = $methodCall->args[2];
        // the third argument may be closure or array
        if ($thirdArg->value instanceof Closure && isset($thirdArg->value->params[0])) {
            $this->fakerVariableToPropertyFetch($thirdArg->value->stmts, $thirdArg->value->params[0]);
            unset($thirdArg->value->params[0]);
        }

        $expr = $this->nodeFactory->createMethodCall(self::THIS, 'state', [$methodCall->args[2]]);
        $return = new Return_($expr);
        if (! isset($methodCall->args[1])) {
            return null;
        }

        if (! $methodCall->args[1] instanceof Arg) {
            return null;
        }

        return $this->createPublicMethod($this->valueResolver->getValue($methodCall->args[1]->value), [$return]);
    }

    public function createEmptyConfigure(): ClassMethod
    {
        $return = new Return_(new Variable(self::THIS));
        return $this->createPublicMethod('configure', [$return]);
    }

    public function appendConfigure(ClassMethod $classMethod, string $name, Closure $closure): void
    {
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable(
            (array) $classMethod->stmts,
            function (Node $node) use ($closure, $name): ?Return_ {
                if (! $node instanceof Return_) {
                    return null;
                }

                if ($node->expr === null) {
                    return null;
                }

                if (isset($closure->params[1])) {
                    $this->fakerVariableToPropertyFetch($closure->stmts, $closure->params[1]);
                    // remove argument $faker
                    unset($closure->params[1]);
                }

                $node->expr = $this->nodeFactory->createMethodCall($node->expr, $name, [$closure]);
                return $node;
            }
        );
    }

    /**
     * @param Node\Stmt[] $stmts
     */
    private function fakerVariableToPropertyFetch(array $stmts, Param $param): void
    {
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($stmts, function (Node $node) use (
            $param
        ): ?PropertyFetch {
            if (! $node instanceof Variable) {
                return null;
            }

            $name = $this->nodeNameResolver->getName($param->var);
            if ($name === null) {
                return null;
            }

            if (! $this->nodeNameResolver->isName($node, $name)) {
                return null;
            }

            // use $this->faker instead of $faker
            return $this->nodeFactory->createPropertyFetch(self::THIS, 'faker');
        });
    }

    /**
     * @param Node\Stmt[] $stmts
     */
    private function createPublicMethod(string $name, array $stmts): ClassMethod
    {
        $methodBuilder = new MethodBuilder($name);
        $methodBuilder->makePublic();
        $methodBuilder->addStmts($stmts);
        return $methodBuilder->getNode();
    }
}

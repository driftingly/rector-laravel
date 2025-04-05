<?php

declare(strict_types=1);

namespace RectorLaravel\NodeFactory;

use PhpParser\Builder\Method;
use PhpParser\Builder\Property;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
use Rector\PhpParser\Node\NodeFactory;
use Rector\PhpParser\Node\Value\ValueResolver;

final class ModelFactoryNodeFactory
{
    /**
     * @readonly
     * @var \Rector\NodeNameResolver\NodeNameResolver
     */
    private $nodeNameResolver;
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\NodeFactory
     */
    private $nodeFactory;
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    /**
     * @readonly
     * @var \Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser
     */
    private $simpleCallableNodeTraverser;
    /**
     * @var string
     */
    private const THIS = 'this';

    public function __construct(NodeNameResolver $nodeNameResolver, NodeFactory $nodeFactory, ValueResolver $valueResolver, SimpleCallableNodeTraverser $simpleCallableNodeTraverser)
    {
        $this->nodeNameResolver = $nodeNameResolver;
        $this->nodeFactory = $nodeFactory;
        $this->valueResolver = $valueResolver;
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
    }

    public function createEmptyFactory(string $name, Expr $expr): Class_
    {
        $class = new Class_($name . 'Factory', [], [
            'startLine' => $expr->getStartLine(),
            'endLine' => $expr->getEndLine(),
        ]);
        $class->extends = new FullyQualified('Illuminate\Database\Eloquent\Factories\Factory');
        $propertyBuilder = new Property('model');
        $propertyBuilder->makeProtected();
        $propertyBuilder->setDefault($expr);

        $property = $propertyBuilder->getNode();
        $class->stmts[] = $property;

        // decorate with namespaced names
        $nameResolver = new NameResolver(null, [
            'replaceNodes' => false,
            'preserveOriginalNames' => true,
        ]);
        $nodeTraverser = new NodeTraverser;
        $nodeTraverser->addVisitor($nameResolver);
        $nodeTraverser->traverse([$class]);

        return $class;
    }

    /**
     * @param \PhpParser\Node\Expr\Closure|\PhpParser\Node\Expr\ArrowFunction $callable
     */
    public function createDefinition($callable): ClassMethod
    {
        if (isset($callable->params[0])) {
            $this->fakerVariableToPropertyFetch($callable->getStmts(), $callable->params[0]);
        }

        return $this->createPublicMethod('definition', $callable->getStmts());
    }

    public function createStateMethod(MethodCall $methodCall): ?ClassMethod
    {
        if (! isset($methodCall->args[2])) {
            return null;
        }

        if (! $methodCall->args[2] instanceof Arg) {
            return null;
        }

        $thirdArgValue = $methodCall->args[2]->value;
        // the third argument may be closure or array
        if (($thirdArgValue instanceof Closure || $thirdArgValue instanceof ArrowFunction) && isset($thirdArgValue->params[0])) {
            $this->fakerVariableToPropertyFetch($thirdArgValue->getStmts(), $thirdArgValue->params[0]);
            unset($thirdArgValue->params[0]);
        }

        $expr = $this->nodeFactory->createMethodCall(self::THIS, 'state', [$methodCall->args[2]]);
        $return = new Return_($expr);
        if (! isset($methodCall->args[1])) {
            return null;
        }

        if (! $methodCall->args[1] instanceof Arg) {
            return null;
        }

        $methodName = $this->valueResolver->getValue($methodCall->args[1]->value);
        if (! is_string($methodName)) {
            return null;
        }

        return $this->createPublicMethod($methodName, [$return]);
    }

    public function createEmptyConfigure(): ClassMethod
    {
        $return = new Return_(new Variable(self::THIS));

        return $this->createPublicMethod('configure', [$return]);
    }

    /**
     * @param \PhpParser\Node\Expr\Closure|\PhpParser\Node\Expr\ArrowFunction $callable
     */
    public function appendConfigure(ClassMethod $classMethod, string $name, $callable): void
    {
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable(
            (array) $classMethod->stmts,
            function (Node $node) use ($callable, $name): ?Return_ {
                if (! $node instanceof Return_) {
                    return null;
                }

                if (! $node->expr instanceof Expr) {
                    return null;
                }

                if (isset($callable->params[1])) {
                    $this->fakerVariableToPropertyFetch($callable->getStmts(), $callable->params[1]);
                    // remove argument $faker
                    unset($callable->params[1]);
                }

                $node->expr = $this->nodeFactory->createMethodCall($node->expr, $name, [$callable]);

                return $node;
            }
        );
    }

    /**
     * @param  Node\Stmt[]  $stmts
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
     * @param  Node\Stmt[]  $stmts
     */
    private function createPublicMethod(string $name, array $stmts): ClassMethod
    {
        $method = new Method($name);
        $method->makePublic();
        $method->addStmts($stmts);

        return $method->getNode();
    }
}

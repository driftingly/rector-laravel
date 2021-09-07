<?php

declare(strict_types=1);

namespace Rector\Laravel\NodeFactory;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\Core\PhpParser\Node\NodeFactory;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;
use Symplify\Astral\ValueObject\NodeBuilder\MethodBuilder;

class ModelFactoryFactory
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeFactory $nodeFactory,
        private ValueResolver $valueResolver,
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser
    ) {
    }

    public function createStateMethod(MethodCall $methodCall): Node\Stmt\ClassMethod
    {
        $closure = $methodCall->args[2]->value;
        if ($closure instanceof Node\Expr\Closure) {
            $this->fakerVariableToPropertyFetch($closure->stmts, $closure->params[0]);
            $closure->params[0] = $this->nodeFactory->createParamFromNameAndType('attributes', new ObjectType('array'));
        }

        return $this->createPublicMethod(
            $this->valueResolver->getValue($methodCall->args[1]->value),
            [
                new Node\Stmt\Return_($this->nodeFactory->createMethodCall('this', 'state', [
                    $methodCall->args[2],
                ])),
            ]
        );
    }

    public function createDefinition(Node\Expr\Closure $closure): Node\Stmt\ClassMethod
    {
        $this->fakerVariableToPropertyFetch($closure->stmts, $closure->params[0]);
        return $this->createPublicMethod('definition', $closure->stmts);
    }

    public function createEmptyConfigure(): Node\Stmt\ClassMethod
    {
        return $this->createPublicMethod('configure', [new Node\Stmt\Return_(new Node\Expr\Variable('this'))]);
    }

    public function appendConfigure(Node\Stmt\ClassMethod $classMethod, string $name, Node\Expr\Closure $closure): void
    {
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable(
            (array) $classMethod->stmts,
            function (Node $node) use ($closure, $name) {
                if (! $node instanceof Node\Stmt\Return_) {
                    return null;
                }
                if ($node->expr === null) {
                    return null;
                }
                $this->fakerVariableToPropertyFetch($closure->stmts, $closure->params[1]);
                unset($closure->params[1]);
                $node->expr = $this->nodeFactory->createMethodCall($node->expr, $name, [$closure]);
                return $node;
            }
        );
    }

    /**
     * @param Node\Stmt[] $stmts
     * @param \PhpParser\Node\Param $param
     */
    private function fakerVariableToPropertyFetch(array $stmts, Node\Param $param): void
    {
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($stmts, function (Node $node) use ($param) {
            if (! $node instanceof Node\Expr\Variable) {
                return null;
            }
            $name = $this->nodeNameResolver->getName($param->var);
            if ($name === null) {
                return null;
            }
            if (! $this->nodeNameResolver->isName($node, $name)) {
                return null;
            }
            return $this->nodeFactory->createPropertyFetch('this', 'faker');
        });
    }

    /**
     * @param Node\Stmt[] $stmts
     */
    private function createPublicMethod(string $name, array $stmts): Node\Stmt\ClassMethod
    {
        $methodBuilder = new MethodBuilder($name);
        $methodBuilder->makePublic();
        $methodBuilder->addStmts($stmts);
        return $methodBuilder->getNode();
    }
}

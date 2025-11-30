<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
use RectorLaravel\ValueObject\ExpectedClassMethodMethodCalls;

final class ExpectedClassMethodAnalyzer
{
    /**
     * @readonly
     */
    private SimpleCallableNodeTraverser $simpleCallableNodeTraverser;
    /**
     * @readonly
     */
    private NodeNameResolver $nodeNameResolver;
    /**
     * @readonly
     */
    private NodeTypeResolver $nodeTypeResolver;
    public function __construct(SimpleCallableNodeTraverser $simpleCallableNodeTraverser, NodeNameResolver $nodeNameResolver, NodeTypeResolver $nodeTypeResolver)
    {
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
        $this->nodeNameResolver = $nodeNameResolver;
        $this->nodeTypeResolver = $nodeTypeResolver;
    }

    public function findExpectedJobCallsWithClassMethod(ClassMethod $classMethod): ?ExpectedClassMethodMethodCalls
    {
        /** @var MethodCall[] $expectedMethodCalls */
        $expectedMethodCalls = [];
        /** @var MethodCall[] $notExpectedMethodCalls */
        $notExpectedMethodCalls = [];
        $reasonsToNotContinue = false;

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($classMethod, function (Node $node) use (
            &$expectedMethodCalls,
            &$notExpectedMethodCalls,
            &$reasonsToNotContinue
        ) {
            if (! $node instanceof MethodCall) {
                return null;
            }

            if (! $this->nodeTypeResolver->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Foundation\Testing\TestCase')
            )) {
                return null;
            }

            if ($this->nodeNameResolver->isName($node->name, 'expectsJobs')) {
                $expectedMethodCalls[] = $node;

                return null;
            }

            if ($this->nodeNameResolver->isName($node->name, 'doesntExpectJobs')) {
                $notExpectedMethodCalls[] = $node;
            }

            if ($node->isFirstClassCallable()) {
                $reasonsToNotContinue = true;
            }

            return null;
        });

        if ($reasonsToNotContinue) {
            return null;
        }

        $expectedItems = $this->findItemsToFake($expectedMethodCalls);
        $notExpectedItems = $this->findItemsToFake($notExpectedMethodCalls);

        return new ExpectedClassMethodMethodCalls(
            $expectedMethodCalls,
            $expectedItems,
            $notExpectedMethodCalls,
            $notExpectedItems
        );
    }

    public function findExpectedEventCallsWithClassMethod(ClassMethod $classMethod): ?ExpectedClassMethodMethodCalls
    {
        /** @var MethodCall[] $expectedMethodCalls */
        $expectedMethodCalls = [];
        /** @var MethodCall[] $notExpectedMethodCalls */
        $notExpectedMethodCalls = [];
        $reasonsToNotContinue = false;

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($classMethod, function (Node $node) use (
            &$expectedMethodCalls,
            &$notExpectedMethodCalls,
            &$reasonsToNotContinue
        ) {
            if (! $node instanceof MethodCall) {
                return null;
            }

            if (! $this->nodeTypeResolver->isObjectType(
                $node->var,
                new ObjectType('Illuminate\Foundation\Testing\TestCase')
            )) {
                return null;
            }

            if ($this->nodeNameResolver->isName($node->name, 'expectsEvents')) {
                $expectedMethodCalls[] = $node;

                return null;
            }

            if ($this->nodeNameResolver->isName($node->name, 'doesntExpectEvents')) {
                $notExpectedMethodCalls[] = $node;
            }

            if ($node->isFirstClassCallable()) {
                $reasonsToNotContinue = true;
            }

            return null;
        });

        if ($reasonsToNotContinue) {
            return null;
        }

        $expectedItems = $this->findItemsToFake($expectedMethodCalls);
        $notExpectedItems = $this->findItemsToFake($notExpectedMethodCalls);

        return new ExpectedClassMethodMethodCalls(
            $expectedMethodCalls,
            $expectedItems,
            $notExpectedMethodCalls,
            $notExpectedItems
        );
    }

    /**
     * @param  MethodCall[]  $methodCalls
     * @return list<String_|ClassConstFetch>
     */
    private function findItemsToFake(array $methodCalls): array
    {
        $items = [];
        foreach ($methodCalls as $methodCall) {
            if (! $methodCall->args[0] instanceof Arg) {
                continue;
            }
            $value = $methodCall->args[0]->value;
            if ($value instanceof String_) {
                $items[] = $value;

                continue;
            }
            if ($value instanceof ClassConstFetch && $this->nodeNameResolver->isName($value->name, 'class')) {
                $items[] = $value;

                continue;
            }
            if ($value instanceof Array_) {
                foreach ($value->items as $arrayItem) {
                    if ($arrayItem->value instanceof ClassConstFetch && $this->nodeNameResolver->isName($arrayItem->value->name, 'class')) {
                        $items[] = $arrayItem->value;

                        continue;
                    }
                    if ($arrayItem->value instanceof String_) {
                        $items[] = $arrayItem->value;
                    }
                }
            }
        }

        return $items;
    }
}

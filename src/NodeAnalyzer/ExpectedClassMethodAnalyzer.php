<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
use RectorLaravel\ValueObject\ExpectedClassMethodMethodCalls;

class ExpectedClassMethodAnalyzer
{
    public function __construct(
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser,
        private NodeNameResolver $nodeNameResolver,
    )
    {
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
            &$reasonsToNotContinue,
        ): void {
            if (! $node instanceof MethodCall) {
                return;
            }

            if ($this->nodeNameResolver->isName($node->name, 'expectsJobs')) {
                $expectedMethodCalls[] = $node;
                return;
            }

            if ($this->nodeNameResolver->isName($node->name, 'doesntExpectJobs')) {
                $notExpectedMethodCalls[] = $node;
            }

            if ($node->isFirstClassCallable()) {
                $reasonsToNotContinue = true;
            }
        });

        if ($reasonsToNotContinue) {
            return null;
        }

        $expectedItems = $this->findClasses($expectedMethodCalls);
        $notExpectedItems = $this->findClasses($notExpectedMethodCalls);

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
            &$reasonsToNotContinue,
        ): void {
            if (! $node instanceof MethodCall) {
                return;
            }

            if ($this->nodeNameResolver->isName($node->name, 'expectsEvents')) {
                $expectedMethodCalls[] = $node;
                return;
            }

            if ($this->nodeNameResolver->isName($node->name, 'doesntExpectEvents')) {
                $notExpectedMethodCalls[] = $node;
            }

            if ($node->isFirstClassCallable()) {
                $reasonsToNotContinue = true;
            }
        });

        if ($reasonsToNotContinue) {
            return null;
        }

        $expectedItems = $this->findClasses($expectedMethodCalls);
        $notExpectedItems = $this->findClasses($notExpectedMethodCalls);

        return new ExpectedClassMethodMethodCalls(
            $expectedMethodCalls,
            $expectedItems,
            $notExpectedMethodCalls,
            $notExpectedItems
        );
    }

    private function findClasses(array $methodCalls): array
    {
        $items = [];
        foreach ($methodCalls as $methodCall) {
            $value = $methodCall->args[0]->value;
            if ($value instanceof Node\Scalar\String_) {
                $items[] = $value->value;
                continue;
            }
            if ($value instanceof Node\Expr\ClassConstFetch && $this->nodeNameResolver->isName($value->name, 'class')) {
                $items[] = $value->class->name;
                continue;
            }
            if ($value instanceof Node\Expr\Array_) {
                foreach ($value->items as $arrayItem) {
                    if ($arrayItem->value instanceof Node\Expr\ClassConstFetch && $this->nodeNameResolver->isName($arrayItem->value->name, 'class')) {
                        $items[] = $arrayItem->value->class->name;
                        continue;
                    }
                    if ($arrayItem->value instanceof Node\Scalar\String_) {
                        $items[] = $arrayItem->value->value;
                    }
                }
            }
        }

        return $items;
    }
}

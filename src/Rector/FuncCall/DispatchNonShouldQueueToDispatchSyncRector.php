<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractScopeAwareRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class DispatchNonShouldQueueToDispatchSyncRector extends AbstractScopeAwareRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Dispatch non ShouldQueue to dispatchSync',
            [
                new CodeSample(
                    '$resultOfJob = dispatch(new SomeJob());',
                    '$resultOfJob = dispatch_sync(new SomeJob());'
                )
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\Assign::class, Node\Stmt\If_::class, Node\Stmt\Foreach_::class];
    }

    /**
     * @param Node\Expr\Assign|Node\Stmt\If_ $node
     */
    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        $hasChanged = false;

        $this->traverseNodesWithCallable(
            $node,
            function (Node $node) use (&$hasChanged, $scope): FuncCall|MethodCall|null {
                if (
                    ($node instanceof FuncCall || $node instanceof MethodCall) &&
                    $this->isName($node, 'dispatch') &&
                    count($node->args) === 1
                ) {
                    $newNode = $this->processCall($node, $scope);

                    if ($newNode !== $node) {
                        $hasChanged = true;
                        return $newNode;
                    }

                    return null;
                }
    
                return null;
            }
        );

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    private function processCall(FuncCall|MethodCall $call, Scope $scope): FuncCall|MethodCall|null
    {
        if (! $call->args[0] instanceof Node\Arg) {
            return null;
        }

        if (
            $scope->getType($call->args[0]->value)->isSuperTypeOf(
                new ObjectType('Illuminate\Contracts\Queue\ShouldQueue')
            )->yes() ||
            $this->isObjectType(
                $call->args[0]->value,
                new ObjectType('Illuminate\Contracts\Queue\ShouldQueue')
            )
        ) {
            return null;
        }

        if ($call instanceof FuncCall) {
            return new FuncCall(
                new Node\Name('dispatch_sync'),
                $call->args
            );
        }

        return new MethodCall(
            $call->var,
            new Node\Name('dispatchSync'),
            $call->args
        );
    }
}

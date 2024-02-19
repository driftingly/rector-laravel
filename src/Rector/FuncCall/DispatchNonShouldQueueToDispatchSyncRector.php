<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\DispatchNonShouldQueueToDispatchSyncRectorTest
 */
class DispatchNonShouldQueueToDispatchSyncRector extends AbstractRector
{
    public function __construct(private readonly ReflectionProvider $reflectionProvider)
    {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Dispatch non ShouldQueue to dispatchSync when using assignment',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$result = dispatch(new SomeJob());
$anotherResult = Bus::dispatch(new SomeJob());
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$result = dispatchSync(new SomeJob());
$anotherResult = Bus::dispatchSync(new SomeJob());
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param  Node\Expr\Assign  $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;

        $this->traverseNodesWithCallable(
            $node,
            function (Node $node) use (&$hasChanged): FuncCall|MethodCall|StaticCall|null {
                if (
                    (
                        $node instanceof FuncCall ||
                        $node instanceof MethodCall ||
                        $node instanceof StaticCall
                    ) &&
                    $this->isName($node->name, 'dispatch') &&
                    count($node->args) === 1
                ) {
                    if (
                        $node instanceof MethodCall &&
                        ! $this->isDispatchablesCall($node) &&
                        ! $this->isCallOnDispatcherContract($node)
                    ) {
                        return null;
                    }

                    if (
                        $node instanceof StaticCall
                        && ! $this->isCallOnBusFacade($node)
                    ) {
                        return null;
                    }

                    $newNode = $this->processCall($node);

                    if ($newNode !== null) {
                        $hasChanged = true;

                        return $newNode;
                    }
                }

                return null;
            }
        );

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    private function processCall(FuncCall|MethodCall|StaticCall $call): FuncCall|MethodCall|StaticCall|null
    {
        static $shouldQueueType = new ObjectType('Illuminate\Contracts\Queue\ShouldQueue');

        if (! $call->args[0] instanceof Arg) {
            return null;
        }

        if (
            $this->getType($call->args[0]->value)->isSuperTypeOf(
                $shouldQueueType
            )->yes() ||
            $this->isObjectType(
                $call->args[0]->value,
                $shouldQueueType
            )
        ) {
            return null;
        }

        $call->name = $call instanceof FuncCall ? new Name('dispatch_sync') : new Identifier('dispatchSync');

        return $call;
    }

    private function isDispatchablesCall(MethodCall $methodCall): bool
    {
        $type = $this->getType($methodCall->var);
        if (! $type instanceof ObjectType) {
            return false;
        }

        try {
            // Will trigger ClassNotFoundException if the class definition is not found
            $reflection = $this->reflectionProvider->getClass(
                $type->getClassName()
            );

            if ($this->usesDispatchablesTrait($reflection)) {
                return true;
            }

        } catch (ClassNotFoundException) {
        }

        return false;
    }

    private function usesDispatchablesTrait(ClassReflection $classReflection): bool
    {
        return in_array(
            'Illuminate\Foundation\Bus\Dispatchable',
            array_keys($classReflection->getTraits(true)),
            true
        );
    }

    private function isCallOnBusFacade(StaticCall $staticCall): bool
    {
        return $this->isName($staticCall->class, 'Illuminate\Support\Facades\Bus');
    }

    private function isCallOnDispatcherContract(MethodCall $methodCall): bool
    {
        return $this->isObjectType($methodCall->var, new ObjectType('Illuminate\Contracts\Bus\Dispatcher'));
    }
}

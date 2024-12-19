<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ClosureType;
use PHPStan\Type\ObjectType;
use Rector\StaticTypeMapper\ValueObject\Type\AliasedObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\DispatchNonShouldQueueToDispatchSyncRectorTest
 */
class DispatchNonShouldQueueToDispatchSyncRector extends AbstractRector
{
    private const string SHOULD_QUEUE_INTERFACE = 'Illuminate\Contracts\Queue\ShouldQueue';

    private const string BUS_FACADE = 'Illuminate\Support\Facades\Bus';

    private const string DISPATCHER_INTERFACE = 'Illuminate\Contracts\Bus\Dispatcher';

    private const string DISPATCHABLE_TRAIT = 'Illuminate\Foundation\Bus\Dispatchable';

    public function __construct(private readonly ReflectionProvider $reflectionProvider) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Dispatch non ShouldQueue jobs to dispatchSync',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
dispatch(new SomeJob());
Bus::dispatch(new SomeJob());
$this->dispatch(new SomeJob());
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
dispatch_sync(new SomeJob());
Bus::dispatchSync(new SomeJob());
$this->dispatchSync(new SomeJob());
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class, MethodCall::class, StaticCall::class];
    }

    /**
     * @param  FuncCall|MethodCall|StaticCall  $node
     */
    public function refactor(Node $node): FuncCall|MethodCall|StaticCall|null
    {
        if (
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
                return $newNode;
            }
        }

        return null;
    }

    private function processCall(FuncCall|MethodCall|StaticCall $call): FuncCall|MethodCall|StaticCall|null
    {
        if (! $call->args[0] instanceof Arg) {
            return null;
        }

        $objectType = new ObjectType(self::SHOULD_QUEUE_INTERFACE);
        $argumentType = $this->getType($call->args[0]->value);

        if (
            $argumentType->isSuperTypeOf($objectType)->yes() ||
            $this->isObjectType($call->args[0]->value, $objectType) ||
            $argumentType instanceof AliasedObjectType && $this->isSubclassOfShouldQueueInterface($argumentType)
        ) {
            return null;
        }

        // Queued closures can only be dispatched from the helper
        if (
            ! ($call instanceof StaticCall && $this->isCallOnBusFacade($call)) && (
                $this->getType(
                    $call->args[0]->value,
                ) instanceof ClosureType ||
                $call->args[0]->value instanceof Closure ||
                $call->args[0]->value instanceof ArrowFunction
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

        if (! $type->isObject()->yes()) {
            return false;
        }

        $objectClassNames = $type->getObjectClassNames();

        if (count($objectClassNames) !== 1) {
            return false;
        }

        try {
            // Will trigger ClassNotFoundException if the class definition is not found
            $reflection = $this->reflectionProvider->getClass($objectClassNames[0]);

            if ($reflection->hasTraitUse(self::DISPATCHABLE_TRAIT)) {
                return true;
            }

        } catch (ClassNotFoundException) {
        }

        return false;
    }

    private function isSubclassOfShouldQueueInterface(AliasedObjectType $aliasedObjectType): bool
    {
        try {
            $reflection = $this->reflectionProvider->getClass(
                $aliasedObjectType->getFullyQualifiedName(),
            );
        } catch (ClassNotFoundException) {
            return false;
        }

        return $reflection->isSubclassOf(self::SHOULD_QUEUE_INTERFACE);
    }

    private function isCallOnBusFacade(StaticCall $staticCall): bool
    {
        return $this->isName($staticCall->class, self::BUS_FACADE);
    }

    private function isCallOnDispatcherContract(MethodCall $methodCall): bool
    {
        return $this->isObjectType($methodCall->var, new ObjectType(self::DISPATCHER_INTERFACE));
    }
}

<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Builder\Method;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Scalar\String_;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use RectorLaravel\ValueObject\ForwardingCall;

final readonly class CallUserFuncAnalyzer
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver,
    )
    {

    }

    public function isCallUserFuncCall(FuncCall $funcCall): bool
    {
        return $this->nodeNameResolver->isNames($funcCall, ['call_user_func', 'call_user_func_array']);
    }

    public function canDetermineMethodFromCallable(FuncCall $funcCall): bool
    {
        return ($funcCall->args[0] ?? false)
            && (
                $funcCall->args[0]->value instanceof Array_
                || (
                    $funcCall->args[0]->value instanceof MethodCall
                    && $funcCall->args[0]->value->isFirstClassCallable()
                )
            );
    }

    public function getForwardedMethod(FuncCall $funcCall): ?ForwardingCall
    {
        $firstArg = $funcCall->args[0];

        if (! $firstArg instanceof Arg) {
            return null;
        }

        if (! $firstArg->value instanceof Array_ && ! $firstArg->value instanceof MethodCall) {
            return null;
        }

        $args = $funcCall->args[1]->value ?? new Array_([]);

        if ($this->nodeNameResolver->isName($funcCall, 'call_user_func')) {
            $args = $this->argsToArray(array_splice($funcCall->args, 1));
        }

        if ($firstArg->value instanceof Array_) {

            if (count($firstArg->value->items) <> 2) {
                return null;
            }

            $type = $this->nodeTypeResolver->getType($firstArg->value->items[0]);

            if ($type->isObject()->no()) {
                return null;
            }

            return new ForwardingCall(
                $firstArg->value->items[0]->value,
                $firstArg->value->items[1]->value,
                $args
            );
        }

        if (! $firstArg->value->isFirstClassCallable()) {
            return null;
        }

        return new ForwardingCall(
            $firstArg->value->var,
            new String_($firstArg->value->name->name),
            $args
        );
    }

    /**
     * @param Arg[] $args
     * @return Array_
     */
    private function argsToArray(array $args): Array_
    {
        return new Array_(array_map(fn(Arg $arg) => new ArrayItem($arg->value), $args));
    }
}

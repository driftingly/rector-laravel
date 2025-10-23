<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use RectorLaravel\ValueObject\ForwardingCall;

final class CallUserFuncAnalyzer
{
    /**
     * @readonly
     */
    private NodeNameResolver $nodeNameResolver;
    /**
     * @readonly
     */
    private NodeTypeResolver $nodeTypeResolver;
    public function __construct(NodeNameResolver $nodeNameResolver, NodeTypeResolver $nodeTypeResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
        $this->nodeTypeResolver = $nodeTypeResolver;
    }

    public function isCallUserFuncCall(FuncCall $funcCall): bool
    {
        return $this->nodeNameResolver->isNames($funcCall, ['call_user_func', 'call_user_func_array']);
    }

    public function canDetermineMethodFromCallable(FuncCall $funcCall): bool
    {
        return isset($funcCall->args[0]) && $funcCall->args[0] instanceof Arg
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

        $args = $funcCall->getArgs()[1]->value ?? new Array_([]);
        $funcArgs = $funcCall->getArgs();

        if ($this->nodeNameResolver->isName($funcCall, 'call_user_func')) {
            $args = $this->argsToArray(array_splice($funcArgs, 1));
        }

        if ($firstArg->value instanceof Array_) {

            if (count($firstArg->value->items) !== 2) {
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

        if (is_null($this->nodeNameResolver->getName($firstArg->value->name))) {
            return null;
        }

        return new ForwardingCall(
            $firstArg->value->var,
            new String_($this->nodeNameResolver->getName($firstArg->value->name)),
            $args
        );
    }

    /**
     * @param  Arg[]  $args
     */
    private function argsToArray(array $args): Array_
    {
        return new Array_(
            array_map(fn (Arg $arg) => new ArrayItem($arg->value), $args)
        );
    }
}

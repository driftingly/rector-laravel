<?php

declare(strict_types=1);

namespace RectorLaravel\NodeFactory;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class RouterRegisterNodeAnalyzer
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

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    public function isRegisterMethodStaticCall($node): bool
    {
        if (! $this->isRegisterName($node->name)) {
            return false;
        }

        if ($node instanceof MethodCall && $this->nodeTypeResolver->isObjectTypes(
            $node->var,
            [new ObjectType('Illuminate\Routing\Router'), new ObjectType('Illuminate\Routing\RouteRegistrar')]
        )) {
            return true;
        }

        return $node instanceof StaticCall && $this->nodeNameResolver->isNames(
            $node->class,
            ['Illuminate\Support\Facades\Route', 'Route']
        );
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Expr $name
     */
    public function isRegisterName($name): bool
    {
        if ($this->isRegisterAnyVerb($name)) {
            return true;
        }

        if ($this->isRegisterMultipleVerbs($name)) {
            return true;
        }

        if ($this->isRegisterAllVerbs($name)) {
            return true;
        }

        return $this->isRegisterFallback($name);
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Expr $name
     */
    public function isRegisterMultipleVerbs($name): bool
    {
        return $this->nodeNameResolver->isName($name, 'match');
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Expr $name
     */
    public function isRegisterAllVerbs($name): bool
    {
        return $this->nodeNameResolver->isName($name, 'any');
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Expr $name
     */
    public function isRegisterAnyVerb($name): bool
    {
        return $this->nodeNameResolver->isNames($name, ['delete', 'get', 'options', 'patch', 'post', 'put']);
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Expr $name
     */
    public function isRegisterFallback($name): bool
    {
        return $this->nodeNameResolver->isName($name, 'fallback');
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Expr $name
     */
    public function isGroup($name): bool
    {
        return $this->nodeNameResolver->isName($name, 'group');
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $call
     * @return string|false|null
     */
    public function getGroupNamespace($call)
    {
        if (! isset($call->args[0]) || ! $call->args[0] instanceof Arg) {
            return null;
        }

        $firstArg = $call->args[0]->value;
        if (! $firstArg instanceof Array_) {
            return null;
        }

        foreach ($firstArg->items as $item) {
            if (! $item instanceof ArrayItem) {
                continue;
            }

            if ($item->key instanceof String_ && $item->key->value === 'namespace') {

                if ($item->value instanceof String_) {
                    return $item->value->value;
                }

                // if we can't find the namespace value we specify it exists but is
                // unreadable with false
                return false;
            }
        }

        return null;
    }
}

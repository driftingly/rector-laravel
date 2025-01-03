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

final readonly class RouterRegisterNodeAnalyzer
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver
    ) {}

    public function isRegisterMethodStaticCall(MethodCall|StaticCall $node): bool
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

    public function isRegisterName(Identifier|Expr $name): bool
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

    public function isRegisterMultipleVerbs(Identifier|Expr $name): bool
    {
        return $this->nodeNameResolver->isName($name, 'match');
    }

    public function isRegisterAllVerbs(Identifier|Expr $name): bool
    {
        return $this->nodeNameResolver->isName($name, 'any');
    }

    public function isRegisterAnyVerb(Identifier|Expr $name): bool
    {
        return $this->nodeNameResolver->isNames($name, ['delete', 'get', 'options', 'patch', 'post', 'put']);
    }

    public function isRegisterFallback(Identifier|Expr $name): bool
    {
        return $this->nodeNameResolver->isName($name, 'fallback');
    }

    public function isGroup(Identifier|Expr $name): bool
    {
        return $this->nodeNameResolver->isName($name, 'group');
    }

    public function getGroupNamespace(MethodCall|StaticCall $call): string|null|false
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

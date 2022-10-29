<?php

declare(strict_types=1);

namespace RectorLaravel\NodeFactory;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class RouterRegisterNodeAnalyzer
{
    public function __construct(
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly NodeTypeResolver $nodeTypeResolver
    ) {
    }

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

        return $node instanceof StaticCall && $this->nodeNameResolver->isName(
            $node->class,
            'Illuminate\Support\Facades\Route'
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
}

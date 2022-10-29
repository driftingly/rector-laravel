<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class LumenRouteRegisteringMethodAnalyzer
{
    public function __construct(
        private readonly NodeTypeResolver $nodeTypeResolver,
        private readonly NodeNameResolver $nodeNameResolver
    ) {
    }

    public function isLumenRoutingClass(MethodCall $methodCall): bool
    {
        return $this->nodeTypeResolver->isObjectType($methodCall->var, new ObjectType('Laravel\Lumen\Routing\Router'));
    }

    public function isRoutesRegisterGroup(Identifier|Expr $name): bool
    {
        return $this->nodeNameResolver->isName($name, 'group');
    }

    public function isRoutesRegisterRoute(Identifier|Expr $name): bool
    {
        return $this->nodeNameResolver->isNames($name, ['delete', 'get', 'options', 'patch', 'post', 'put']);
    }
}

<?php

declare(strict_types=1);

namespace Rector\Laravel\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class LumenRouteRegisteringMethodAnalyzer
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private NodeNameResolver $nodeNameResolver
    ) {
    }

    public function isLumenRoutingClass(MethodCall $methodCall): bool
    {
        return $this->nodeTypeResolver->isObjectType($methodCall->var, new ObjectType('Laravel\Lumen\Routing\Router'));
    }

    public function isRoutesRegisterGroup(\PhpParser\Node\Identifier|\PhpParser\Node\Expr $name): bool
    {
        return $this->nodeNameResolver->isName($name, 'group');
    }

    public function isRoutesRegisterRoute(\PhpParser\Node\Identifier|\PhpParser\Node\Expr $name): bool
    {
        return $this->nodeNameResolver->isNames($name, ['delete', 'get', 'options', 'patch', 'post', 'put']);
    }
}

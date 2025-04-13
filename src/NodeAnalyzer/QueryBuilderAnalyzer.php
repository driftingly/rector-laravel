<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class QueryBuilderAnalyzer
{
    /**
     * @readonly
     */
    private NodeTypeResolver $nodeTypeResolver;
    /**
     * @readonly
     */
    private NodeNameResolver $nodeNameResolver;
    public function __construct(NodeTypeResolver $nodeTypeResolver, NodeNameResolver $nodeNameResolver)
    {
        $this->nodeTypeResolver = $nodeTypeResolver;
        $this->nodeNameResolver = $nodeNameResolver;
    }

    protected static function modelType(): ObjectType
    {
        return new ObjectType('Illuminate\Database\Eloquent\Model');
    }

    protected static function queryBuilderType(): ObjectType
    {
        return new ObjectType('Illuminate\Contracts\Database\Query\Builder');
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    public function isMatchingCall($node, string $method): bool
    {
        if (! $this->nodeNameResolver->isName($node->name, $method)) {
            return false;
        }

        if ($node instanceof StaticCall) {
            return $this->isProxyCall($node);
        }

        return $this->nodeTypeResolver->isObjectType($node->var, self::queryBuilderType());
    }

    public function isProxyCall(StaticCall $staticCall): bool
    {
        if (! $this->nodeTypeResolver->isObjectType($staticCall->class, self::modelType())) {
            return false;
        }

        $methodName = $this->nodeNameResolver->getName($staticCall->name);
        if (! is_string($methodName)) {
            return false;
        }

        $classType = $this->nodeTypeResolver->getType($staticCall->class);

        if ($classType->isObject()->no()) {
            return false;
        }

        /** @phpstan-ignore method.notFound */
        $reflectionClass = $classType->getClassReflection();

        /** @phpstan-ignore phpstanApi.instanceofAssumption */
        if (! $reflectionClass instanceof ClassReflection) {
            return false;
        }

        return ! $reflectionClass->hasNativeMethod($methodName);
    }
}

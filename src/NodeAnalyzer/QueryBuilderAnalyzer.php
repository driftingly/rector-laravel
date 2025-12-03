<?php

namespace RectorLaravel\NodeAnalyzer;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Rector\Exception\ShouldNotHappenException;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final readonly class QueryBuilderAnalyzer
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private NodeNameResolver $nodeNameResolver,
    ) {}

    protected static function modelType(): ObjectType
    {
        return new ObjectType('Illuminate\Database\Eloquent\Model');
    }

    protected static function queryBuilderType(): ObjectType
    {
        return new ObjectType('Illuminate\Contracts\Database\Query\Builder');
    }

    protected static function eloquentQueryBuilderType(): ObjectType
    {
        return new ObjectType('Illuminate\Database\Eloquent\Builder');
    }

    /**
     * Determine if a Method or Static call is on a Query Builder instance.
     *
     * @throws ShouldNotHappenException
     */
    public function isMatchingCall(MethodCall|StaticCall $node, string $method): bool
    {
        if (! $this->nodeNameResolver->isName($node->name, $method)) {
            return false;
        }

        if ($node instanceof StaticCall) {
            return $this->isProxyCall($node);
        }

        return $this->nodeTypeResolver->isObjectType($node->var, self::queryBuilderType());
    }

    /**
     * Determine if a Static call is being forwarded to a Query Builder object from a Model
     *
     * @throws ShouldNotHappenException
     */
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

    /**
     * Resolve the Model being used by an instance of an Eloquent Query Builder
     *
     * @return ObjectType|null
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    public function resolveQueryBuilderModel(Type $objectType, Scope $scope): ?Type
    {
        if ($objectType->isObject()->no() || $objectType->isSuperTypeOf(self::queryBuilderType())->no()) {
            throw new InvalidArgumentException('Object type must be an Eloquent query builder.');
        }

        $extendedPropertyReflection = $objectType->getInstanceProperty('model', $scope);
        $modelType = $extendedPropertyReflection->getReadableType();

        if ($modelType->isObject()->no()) {
            return null;
        }

        if ($modelType->isSuperTypeOf(self::modelType())->no()) {
            return null;
        }

        /** @phpstan-ignore return.type */
        return $modelType;
    }

    /**
     * Determine if a node is an Eloquent Query Builder for a particular Eloquent Model
     *
     * @throws \PHPStan\ShouldNotHappenException
     * @throws ShouldNotHappenException
     */
    public function isQueryUsingModel(Node $node, ObjectType $objectType): bool
    {
        $classType = $this->nodeTypeResolver->getType($node);

        if (self::eloquentQueryBuilderType()->isSuperTypeOf($classType)->no()) {
            return false;
        }
        $type = $classType->getTemplateType(Builder::class, 'TModel');
        if ($type->isObject()->no() || self::modelType()->isSuperTypeOf($type)->no()) {
            return false;
        }

        /** @phpstan-ignore method.notFound */
        return $type->getClassName() === $objectType->getClassName();
    }
}

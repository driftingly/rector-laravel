<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PHPStan\ScopeFetcher;

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
        return new ObjectType('Illuminate\Contracts\Database\Eloquent\Builder');
    }

    /**
     * Determine if a Method or Static call is on a Query Builder instance.
     *
     * @param  MethodCall|StaticCall $node
     * @param  string $method
     * @return bool
     * @throws \Rector\Exception\ShouldNotHappenException
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
     * @param  StaticCall $staticCall
     * @return bool
     * @throws \Rector\Exception\ShouldNotHappenException
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
     * @param  ObjectType $objectType
     * @param  Scope $scope
     * @return ObjectType|null
     * @throws \PHPStan\ShouldNotHappenException
     */
    public function resolveQueryBuilderModel(ObjectType $objectType, Scope $scope): ?Type
    {
        if ($objectType->isSuperTypeOf(self::eloquentQueryBuilderType())->no()) {
            throw new \InvalidArgumentException('object must be super of Eloquent Builder');
        }

        $modelProperty = $objectType->getInstanceProperty('model', $scope);
        $modelType = $modelProperty->getNativeType();

        if ($modelType->isObject()->no()) {
            return null;
        }

        if ($modelType->isSuperTypeOf(self::modelType())->no()) {
            return null;
        }

        return $modelType;
    }

    /**
     * Determine if a node is an Eloquent Query Builder for a particular Eloquent Model
     *
     * @param  Node $node
     * @param  ObjectType $model
     * @return bool
     * @throws \PHPStan\ShouldNotHappenException
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    public function isQueryUsingModel(Node $node, ObjectType $model): bool
    {
        $classType = $this->nodeTypeResolver->getType($node);
        $scope = ScopeFetcher::fetch($node);

        /** @var $classType ObjectType */
        if ($classType->isObject()->no()) {
            return false;
        }

        if ($classType->isSuperTypeOf(self::eloquentQueryBuilderType())->no()) {
            return false;
        }

        if ($this->resolveQueryBuilderModel($classType, $scope)->isSuperTypeOf($model)->yes()) {
            return true;
        }

        return false;
    }
}

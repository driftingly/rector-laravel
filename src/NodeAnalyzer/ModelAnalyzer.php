<?php

namespace RectorLaravel\NodeAnalyzer;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use ReflectionException;
use Throwable;

class ModelAnalyzer
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    protected static function relationType(): ObjectType
    {
        return new ObjectType('Illuminate\Database\Eloquent\Relations\Relation');
    }

    /**
     * Returns the table name of a model
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function getTable($model): ?string
    {
        $model = $this->resolveModelClassToInstance($model);

        if (! $model instanceof Model) {
            return null;
        }

        $table = $model->getTable();

        if (! is_string($table)) {
            return null;
        }

        return $table;
    }

    /**
     * Returns the primary key for a model
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws ReflectionException
     */
    public function getPrimaryKey($model): ?string
    {
        $model = $this->resolveModelClassToInstance($model);

        if (! $model instanceof Model) {
            return null;
        }

        $keyName = $model->getKeyName();

        if (! is_string($keyName)) {
            return null;
        }

        return $keyName;
    }

    /**
     * @param  class-string<Model>|ObjectType  $model
     */
    public function isQueryScopeOnModel($model, string $scopeName, Scope $scope): bool
    {
        if (! is_string($model)) {
            /** @var class-string<Model> $model */
            $model = $model->getClassName();
        }

        $classReflection = $this->getClass($model);

        if ($classReflection->hasMethod('scope' . ucfirst($scopeName))) {
            return true;
        }

        if (! $classReflection->hasMethod($scopeName)) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getMethod($scopeName, $scope);

        return $this->usesScopeAttribute($extendedMethodReflection);
    }

    /**
     * @param  class-string<Model>|ObjectType  $model
     */
    public function isRelationshipOnModel($model, string $relationName, Scope $scope): bool
    {
        if (! is_string($model)) {
            /** @var class-string<Model> $model */
            $model = $model->getClassName();
        }

        $classReflection = $this->getClass($model);

        if (! $classReflection->hasMethod($relationName)) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getMethod($relationName, $scope);

        foreach ($extendedMethodReflection->getVariants() as $extendedParametersAcceptor) {
            $returnType = $extendedParametersAcceptor->getReturnType();

            if ($returnType->isObject()->maybe()) {
                continue;
            }

            if (self::relationType()->isSuperTypeOf($returnType)->yes()) {
                return true;
            }
        }

        return false;
    }

    private function usesScopeAttribute(ExtendedMethodReflection $extendedMethodReflection): bool
    {
        foreach ($extendedMethodReflection->getAttributes() as $attributeReflection) {
            if ($attributeReflection->getName() === 'Illuminate\Database\Eloquent\Attributes\Scope') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the ClassReflectionFor the Model
     *
     * @param  class-string<Model>  $class
     *
     * @throws InvalidArgumentException
     */
    private function getClass(string $class): ClassReflection
    {
        if (! $this->reflectionProvider->hasClass($class)) {
            throw new InvalidArgumentException('Class not found');
        }

        $classReflection = $this->reflectionProvider->getClass($class);

        if (! $classReflection->isClass()) {
            throw new InvalidArgumentException('Class string does not resolve to class');
        }

        if (! $classReflection->isSubclassOfClass($this->reflectionProvider->getClass(Model::class))) {
            throw new InvalidArgumentException('Class is not subclass of Model');
        }

        return $classReflection;
    }

    /**
     * Create an instance of the Model to interact with
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws ReflectionException
     */
    private function resolveModelClassToInstance($model): ?Model
    {
        $classReflection = is_string($model)
            ? $this->getClass($model)
            : $model->getObjectClassReflections()[0];

        if ($classReflection->isAbstract()) {
            return null;
        }

        try {
            /** @var Model $instance */
            $instance = $classReflection->getNativeReflection()->newInstance();
        } catch (Throwable $exception) {
            /** @var Model $instance */
            $instance = $classReflection->getNativeReflection()->newInstanceWithoutConstructor();
        }

        return $instance;
    }
}

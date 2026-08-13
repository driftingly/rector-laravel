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

final readonly class ModelAnalyzer
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

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
    public function getTable(string|ObjectType $model): ?string
    {
        if (! is_string($model)) {
            /** @var class-string<Model> $model */
            $model = $model->getClassName();
        }

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
     * The framework sorts the segments the table is made of, so it is resolved from whichever of the two models can
     * be instantiated, as one of them can be a class which is only being analysed rather than autoloaded.
     *
     * @param  class-string<Model>|ObjectType  $model
     * @param  class-string<Model>|ObjectType  $related
     */
    public function getJoiningTable(string|ObjectType $model, string|ObjectType $related): ?string
    {
        $modelInstance = $this->tryResolveModelClassToInstance($model);
        $relatedInstance = $this->tryResolveModelClassToInstance($related);

        $table = match (true) {
            $modelInstance instanceof Model => $modelInstance->joiningTable($this->resolveClassName($related), $relatedInstance),
            $relatedInstance instanceof Model => $relatedInstance->joiningTable($this->resolveClassName($model), $modelInstance),
            default => null,
        };

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
    public function getPrimaryKey(string|ObjectType $model): ?string
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
    public function isQueryScopeOnModel(string|ObjectType $model, string $scopeName, Scope $scope): bool
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
    public function isRelationshipOnModel(string|ObjectType $model, string $relationName, Scope $scope): bool
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
     * The model is resolved from the analysed code, so it can be a class which is unknown or not a model at all.
     *
     * @param  class-string<Model>|ObjectType  $model
     */
    private function tryResolveModelClassToInstance(string|ObjectType $model): ?Model
    {
        try {
            return $this->resolveModelClassToInstance($model);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  class-string<Model>|ObjectType  $model
     * @return class-string<Model>
     */
    private function resolveClassName(string|ObjectType $model): string
    {
        if (is_string($model)) {
            return $model;
        }

        /** @var class-string<Model> $className */
        $className = $model->getClassName();

        return $className;
    }

    /**
     * Create an instance of the Model to interact with
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws ReflectionException
     */
    private function resolveModelClassToInstance(string|ObjectType $model): ?Model
    {
        $classReflection = is_string($model)
            ? $this->getClass($model)
            : ($model->getObjectClassReflections()[0] ?? null);

        if (! $classReflection instanceof ClassReflection || $classReflection->isAbstract()) {
            return null;
        }

        try {
            /** @var Model $instance */
            $instance = $classReflection->getNativeReflection()->newInstance();

            return $instance;
        } catch (Throwable) {
        }

        try {
            /** @var Model $instance */
            $instance = $classReflection->getNativeReflection()->newInstanceWithoutConstructor();
        } catch (Throwable) {
            // a class which is only being analysed cannot be loaded, so no instance can be made of it
            return null;
        }

        // the class attributes are applied by the constructor, so they are applied manually when it cannot run
        if (method_exists($instance, 'initializeModelAttributes')) {
            try {
                $instance->initializeModelAttributes();
            } catch (Throwable) {
            }
        }

        return $instance;
    }
}

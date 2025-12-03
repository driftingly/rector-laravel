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

class ModelAnalyzer
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    /**
     * Returns the table name of a model
     *
     * @param  class-string<Model>|ObjectType  $model
     *
     * @throws InvalidArgumentException|ReflectionException
     */
    public function getTable(string|ObjectType $model): ?string
    {
        $table = $this->resolveModelClassToInstance($model)->getTable();

        if (! is_string($table)) {
            return null;
        }

        return $table;
    }

    /**
     * Returns the primary key for a model
     *
     * @param class-string<Model>|ObjectType $model
     * @throws ReflectionException
     */
    public function getPrimaryKey(string|ObjectType $model): ?string
    {
        $keyName = $this->resolveModelClassToInstance($model)->getKeyName();

        if (! is_string($keyName)) {
            return null;
        }

        return $keyName;
    }

    /**
     * @param string|ObjectType $model
     * @param string $methodName
     * @return bool
     */
    public function isQueryScopeOnModel(string|ObjectType $model, string $scopeName, Scope $scope): bool
    {
        if (! is_string($model)) {
            $model = $model->getClassName();
        }

        $classReflection = $this->getClass($model);

        if ($classReflection->hasMethod('scope' . ucfirst($scopeName))) {
            return true;
        }

        $method = $classReflection->getMethod($scopeName, $scope);

        if ($this->usesScopeAttribute($method))  {
            return true;
        }

        return false;
    }

    private function usesScopeAttribute(ExtendedMethodReflection $methodReflection): bool
    {
        foreach ($methodReflection->getAttributes() as $attribute) {
            if ($attribute->getName() === 'Illuminate\Database\Eloquent\Attributes\Scope') {
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
     * @param class-string<Model>|ObjectType $model
     * @return Model
     * @throws ReflectionException
     */
    private function resolveModelClassToInstance(string|ObjectType $model): Model
    {
        if (! is_string($model)) {
            $model = $model->getClassName();
        }

        $classReflection = $this->getClass($model);

        /** @var Model $instance */
        $instance = $classReflection->getNativeReflection()->newInstanceWithoutConstructor();

        return $instance;
    }
}

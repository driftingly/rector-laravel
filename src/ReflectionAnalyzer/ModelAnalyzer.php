<?php

namespace RectorLaravel\ReflectionAnalyzer;

use Illuminate\Database\Eloquent\Model;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;

class ModelAnalyzer
{
    public function __construct
    (
        private readonly ReflectionProvider $reflectionProvider
    )
    {
    }

    /**
     * Returns the table name of a model
     *
     * @param class-string<Model> $class
     * @throws \Exception
     */
    public function getTable(string $class): ?string
    {
        $classReflection = $this->getClass($class);

        /** @var Model $instance */
        $instance = $classReflection->getNativeReflection()->newInstanceWithoutConstructor();
        $table = $instance->getTable();
        if (! is_string($table)) {
            return null;
        }

        return $table;
    }

    public function getPrimaryKey(string $class): ?string
    {
        $classReflection = $this->getClass($class);

        /** @var Model $instance */
        $instance = $classReflection->getNativeReflection()->newInstanceWithoutConstructor();
        $keyName = $instance->getKeyName();
        if (! is_string($keyName)) {
            return null;
        }

        return $keyName;
    }

    /**
     * @param class-string<Model> $class
     * @throws \Exception
     */
    private function getClass(string $class): ClassReflection
    {
        if (! $this->reflectionProvider->hasClass($class)) {
            throw new \Exception('Class not found');
        }

        $classReflection = $this->reflectionProvider->getClass($class);

        if (! $classReflection->isClass()) {
            throw new \Exception('Class is not class');
        }

        if (! $classReflection->isSubclassOf(Model::class)) {
            throw new \Exception('Class is not subclass of Model');
        }

        return $classReflection;
    }
}

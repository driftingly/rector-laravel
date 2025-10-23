<?php

namespace RectorLaravel\NodeAnalyzer;

use Exception;
use Illuminate\Database\Eloquent\Model;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;

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

    /**
     * Returns the table name of a model
     *
     * @param  class-string<Model>  $class
     *
     * @throws Exception
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

    /**
     * @param  class-string<Model>  $class
     */
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
     * @param  class-string<Model>  $class
     *
     * @throws Exception
     */
    private function getClass(string $class): ClassReflection
    {
        if (! $this->reflectionProvider->hasClass($class)) {
            throw new Exception('Class not found');
        }

        $classReflection = $this->reflectionProvider->getClass($class);

        if (! $classReflection->isClass()) {
            throw new Exception('Class is not class');
        }

        if (! $classReflection->isSubclassOfClass($this->reflectionProvider->getClass(Model::class))) {
            throw new Exception('Class is not subclass of Model');
        }

        return $classReflection;
    }
}

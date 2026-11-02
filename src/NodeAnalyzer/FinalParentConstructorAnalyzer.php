<?php

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use Rector\Reflection\ReflectionResolver;
use Rector\ValueObject\MethodName;

final readonly class FinalParentConstructorAnalyzer
{
    public function __construct(
        private ReflectionResolver $reflectionResolver,
    ) {}

    /**
     * A constructor dependency cannot be added when the nearest parent constructor is final,
     * e.g. Filament actions and form fields.
     */
    public function hasFinalParentConstructor(Class_ $class): bool
    {
        if ($class->getMethod(MethodName::CONSTRUCT) instanceof ClassMethod) {
            return false;
        }

        $classReflection = $this->reflectionResolver->resolveClassReflection($class);

        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if (! $parentClassReflection->hasNativeMethod(MethodName::CONSTRUCT)) {
                continue;
            }

            return $parentClassReflection->getNativeMethod(MethodName::CONSTRUCT)->isFinalByKeyword()->yes();
        }

        return false;
    }
}

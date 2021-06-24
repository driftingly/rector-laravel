<?php

declare(strict_types=1);

namespace Rector\Laravel\Reflection;

use PhpParser\Node\Expr\ClassConstFetch;
use PHPStan\Reflection\ConstantReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class ClassConstantReflectionResolver
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver
    ) {
    }

    public function resolveFromClassConstFetch(ClassConstFetch $classConstFetch): ?ConstantReflection
    {
        $constClassType = $this->nodeTypeResolver->resolve($classConstFetch->class);
        if (! $constClassType instanceof TypeWithClassName) {
            return null;
        }

        $className = $constClassType->getClassName();
        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        $constantName = $this->nodeNameResolver->getName($classConstFetch->name);
        if ($constantName === null) {
            return null;
        }

        if (! $classReflection->hasConstant($constantName)) {
            return null;
        }

        return $classReflection->getConstant($constantName);
    }
}

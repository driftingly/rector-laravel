<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Naming\Naming\PropertyNaming;
use Rector\NodeManipulator\ClassDependencyManipulator;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpParser\Node\NodeFactory;
use Rector\PostRector\ValueObject\PropertyMetadata;
use Rector\Reflection\ReflectionResolver;
use Rector\StaticTypeMapper\Resolver\ClassNameFromObjectTypeResolver;
use Rector\ValueObject\MethodName;

/**
 * Resolves an expression of a given type inside a class, adding a constructor
 * dependency when none is available. Replaces the removed core
 * Rector\Transform\NodeAnalyzer\FuncCallStaticCallToMethodCallAnalyzer.
 */
final readonly class FuncCallStaticCallToMethodCallAnalyzer
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private ReflectionResolver $reflectionResolver,
        private NodeNameResolver $nodeNameResolver,
        private NodeFactory $nodeFactory,
        private PropertyNaming $propertyNaming,
        private ClassDependencyManipulator $classDependencyManipulator,
    ) {}

    public function matchTypeProvidingExpr(
        Class_ $class,
        ClassMethod $classMethod,
        ObjectType $objectType,
    ): MethodCall|PropertyFetch|Variable|null {
        $expr = $this->resolveExistingExpr($class, $classMethod, $objectType);

        if ($expr instanceof Expr) {
            if ($expr instanceof Variable) {
                $variableName = (string) $this->nodeNameResolver->getName($expr);
                $classMethod->params[] = $this->nodeFactory->createParamFromNameAndType($variableName, $objectType);
            }

            return $expr;
        }

        // cannot add a constructor dependency when the parent constructor is final
        if ($this->hasFinalParentConstructor($class)) {
            return null;
        }

        $propertyName = $this->propertyNaming->fqnToVariableName($objectType);
        $this->classDependencyManipulator->addConstructorDependency(
            $class,
            new PropertyMetadata($propertyName, $objectType)
        );

        return new PropertyFetch(new Variable('this'), $propertyName);
    }

    private function resolveExistingExpr(
        Class_ $class,
        ClassMethod $classMethod,
        ObjectType $objectType
    ): MethodCall|PropertyFetch|Variable|null {
        $className = (string) $this->nodeNameResolver->getName($class);
        $classReflection = $this->reflectionProvider->getClass($className);

        // A. existing method returning the type
        $nativeReflection = $classReflection->getNativeReflection();
        foreach ($nativeReflection->getMethods() as $reflectionMethod) {
            $methodReflection = $classReflection->getNativeMethod($reflectionMethod->getName());
            $returnType = ParametersAcceptorSelector::combineAcceptors($methodReflection->getVariants())
                ->getReturnType();

            if ($this->isMatchingType($returnType, $objectType)) {
                return new MethodCall(new Variable('this'), $methodReflection->getName());
            }
        }

        // B. existing property of the type
        foreach ($nativeReflection->getProperties() as $reflectionProperty) {
            $readableType = $classReflection->getNativeProperty($reflectionProperty->getName())
                ->getReadableType();

            if ($this->isMatchingType($readableType, $objectType)) {
                return new PropertyFetch(new Variable('this'), $reflectionProperty->getName());
            }
        }

        // C. inside a constructor - reuse a local variable of the type
        if ($this->nodeNameResolver->isName($classMethod, MethodName::CONSTRUCT)) {
            return new Variable($this->propertyNaming->fqnToVariableName($objectType));
        }

        return null;
    }

    private function isMatchingType(Type $type, ObjectType $objectType): bool
    {
        if ($type instanceof MixedType) {
            return false;
        }

        $type = TypeCombinator::removeNull($type);
        if (ClassNameFromObjectTypeResolver::resolve($type) === null) {
            return false;
        }

        return $type->equals($objectType);
    }

    private function hasFinalParentConstructor(Class_ $class): bool
    {
        if ($class->getMethod(MethodName::CONSTRUCT) instanceof ClassMethod) {
            return false;
        }

        $classReflection = $this->reflectionResolver->resolveClassReflection($class);
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getAncestors() as $ancestor) {
            if ($ancestor->getName() === $classReflection->getName()) {
                continue;
            }

            if (! $ancestor->hasNativeMethod(MethodName::CONSTRUCT)) {
                continue;
            }

            return $ancestor->getNativeMethod(MethodName::CONSTRUCT)
                ->isFinalByKeyword()
                ->yes();
        }

        return false;
    }
}

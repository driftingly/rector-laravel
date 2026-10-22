<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\Naming\Naming\PropertyNaming;
use Rector\NodeManipulator\ClassDependencyManipulator;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\PhpParser\Node\NodeFactory;
use Rector\PostRector\ValueObject\PropertyMetadata;
use Rector\Reflection\ReflectionResolver;
use Rector\ValueObject\MethodName;
use RectorLaravel\NodeFactory\PropertyFetchFactory;

final readonly class FuncCallStaticCallToMethodCallAnalyzer
{
    public function __construct(
        private TypeProvidingExprFromClassResolver $typeProvidingExprFromClassResolver,
        private PropertyNaming $propertyNaming,
        private NodeNameResolver $nodeNameResolver,
        private NodeFactory $nodeFactory,
        private PropertyFetchFactory $propertyFetchFactory,
        private ClassDependencyManipulator $classDependencyManipulator,
        private ReflectionResolver $reflectionResolver,
    ) {}

    public function matchTypeProvidingExpr(
        Class_ $class,
        ClassMethod $classMethod,
        ObjectType $objectType
    ): MethodCall|PropertyFetch|Variable|null {
        $expr = $this->typeProvidingExprFromClassResolver->resolveTypeProvidingExprFromClass(
            $class,
            $classMethod,
            $objectType
        );

        if ($expr instanceof Expr) {
            if ($expr instanceof Variable) {
                $this->addClassMethodParamForVariable($expr, $objectType, $classMethod);
            }

            return $expr;
        }

        // Cannot add constructor dependency when nearest parent constructor is final
        if ($this->hasFinalParentConstructor($class)) {
            return null;
        }

        $propertyName = $this->propertyNaming->fqnToVariableName($objectType);
        $this->classDependencyManipulator->addConstructorDependency(
            $class,
            new PropertyMetadata($propertyName, $objectType)
        );

        return $this->propertyFetchFactory->createFromType($objectType);
    }

    private function addClassMethodParamForVariable(
        Variable $variable,
        ObjectType $objectType,
        ClassMethod|Function_ $functionLike
    ): void {
        /** @var string $variableName */
        $variableName = $this->nodeNameResolver->getName($variable);

        // add variable to __construct as dependency
        $functionLike->params[] = $this->nodeFactory->createParamFromNameAndType($variableName, $objectType);
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

        // nearest parent constructor decides
        foreach ($classReflection->getParents() as $parentClassReflection) {
            if (! $parentClassReflection->hasNativeMethod(MethodName::CONSTRUCT)) {
                continue;
            }

            return $parentClassReflection->getNativeMethod(MethodName::CONSTRUCT)
                ->isFinalByKeyword()
                ->yes();
        }

        return false;
    }
}

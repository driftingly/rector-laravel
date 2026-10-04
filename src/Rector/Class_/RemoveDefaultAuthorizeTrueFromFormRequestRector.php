<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Reflection\ReflectionResolver;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\RemoveDefaultAuthorizeTrueFromFormRequestRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/blob/5.7/src/Illuminate/Foundation/Http/FormRequest.php
 *
 * @see RemoveDefaultAuthorizeTrueFromFormRequestRectorTest
 */
final class RemoveDefaultAuthorizeTrueFromFormRequestRector extends AbstractRector implements ComposerPackageConstraintInterface
{
    private const string AUTHORIZE = 'authorize';

    public function __construct(
        private readonly ReflectionResolver $reflectionResolver,
        private readonly ValueResolver $valueResolver,
    ) {}

    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=5.7');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove the authorize() method from FormRequest classes when it only returns true, as passesAuthorization() defaults to true when the method does not exist',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Foundation\Http\FormRequest'))) {
            return null;
        }

        $classMethod = $node->getMethod(self::AUTHORIZE);
        if (! $classMethod instanceof ClassMethod || ! $this->isReturningOnlyTrue($classMethod)) {
            return null;
        }

        if ($this->isAuthorizeInherited($node)) {
            return null;
        }

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $classMethod) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }

    private function isReturningOnlyTrue(ClassMethod $classMethod): bool
    {
        if ($classMethod->params !== [] || $classMethod->stmts === null || count($classMethod->stmts) !== 1) {
            return false;
        }

        $stmt = $classMethod->stmts[0];
        if (! $stmt instanceof Return_ || ! $stmt->expr instanceof Expr) {
            return false;
        }

        return $this->valueResolver->isTrue($stmt->expr);
    }

    /**
     * Removing the method would expose an authorize() from a parent, trait or interface
     */
    private function isAuthorizeInherited(Class_ $class): bool
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($class);
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        $parentClassReflection = $classReflection->getParentClass();
        if ($parentClassReflection instanceof ClassReflection && $parentClassReflection->hasNativeMethod(self::AUTHORIZE)) {
            return true;
        }

        foreach ($classReflection->getTraits(true) as $traitReflection) {
            if ($traitReflection->hasNativeMethod(self::AUTHORIZE)) {
                return true;
            }
        }

        foreach ($classReflection->getInterfaces() as $interfaceReflection) {
            if ($interfaceReflection->hasNativeMethod(self::AUTHORIZE)) {
                return true;
            }
        }

        return false;
    }
}

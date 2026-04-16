<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;

final readonly class ScopeAnalyzer
{
    private const string SCOPE_ATTRIBUTE = 'Illuminate\Database\Eloquent\Attributes\Scope';

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver,
        private PhpAttributeAnalyzer $phpAttributeAnalyzer,
    ) {}

    /**
     * Determine if a class method is a named query scope (convention-based, with "scope" prefix).
     *
     * A named scope must:
     * 1. Have a name matching "scope" + uppercase char + rest
     * 2. Have at least one parameter where the first parameter is either untyped or typed as a Builder
     * 3. Have no return type, a void return type, or a Builder return type
     */
    public function isNamedScope(ClassMethod $classMethod): bool
    {
        $name = $this->nodeNameResolver->getName($classMethod);

        if ($name === null) {
            return false;
        }

        if (! str_starts_with($name, 'scope') || strlen($name) <= 5 || ! ctype_upper($name[5])) {
            return false;
        }

        return $this->hasBuilderFirstParameter($classMethod) && $this->hasScopeReturnType($classMethod);
    }

    /**
     * Determine if a class method is a scope (either by name convention or #[Scope] attribute).
     */
    public function isScopeMethod(ClassMethod $classMethod): bool
    {
        if ($this->isNamedScope($classMethod)) {
            return true;
        }

        return $this->phpAttributeAnalyzer->hasPhpAttribute($classMethod, self::SCOPE_ATTRIBUTE);
    }

    /**
     * Check that the first parameter is either untyped or typed as a query Builder.
     */
    private function hasBuilderFirstParameter(ClassMethod $classMethod): bool
    {
        if ($classMethod->params === []) {
            return false;
        }

        $firstParam = $classMethod->params[0];

        // Untyped parameter — common pattern: scopeActive($query)
        if ($firstParam->type === null) {
            return true;
        }

        // Typed parameter — check if it's an Eloquent Builder type
        return $this->nodeTypeResolver->isObjectType(
            $firstParam->type,
            new ObjectType('Illuminate\Database\Eloquent\Builder')
        );
    }

    /**
     * Check that the method has a return type compatible with a query scope.
     * Query scopes typically have no return type, void, or return a Builder.
     */
    private function hasScopeReturnType(ClassMethod $classMethod): bool
    {
        if ($classMethod->returnType === null) {
            return true;
        }

        if ($classMethod->returnType instanceof Identifier && $classMethod->returnType->toString() === 'void') {
            return true;
        }

        return $this->nodeTypeResolver->isObjectType(
            $classMethod->returnType,
            new ObjectType('Illuminate\Database\Eloquent\Builder')
        );
    }
}

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

    private const string ELOQUENT_BUILDER = 'Illuminate\Database\Eloquent\Builder';

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver,
        private PhpAttributeAnalyzer $phpAttributeAnalyzer,
    ) {}

    /**
     * Checks for the "scope" + uppercase char naming convention, a Builder-typed
     * first parameter, and a void/Builder/untyped return.
     */
    public function isNamedScope(ClassMethod $classMethod): bool
    {
        $name = $this->nodeNameResolver->getName($classMethod);

        if ($name === null || ! str_starts_with($name, 'scope') || strlen($name) <= 5 || ! ctype_upper($name[5])) {
            return false;
        }

        return $this->hasBuilderFirstParameter($classMethod) && $this->hasScopeReturnType($classMethod);
    }

    /**
     * Named scope OR #[Scope] attribute.
     */
    public function isScopeMethod(ClassMethod $classMethod): bool
    {
        return $this->isNamedScope($classMethod)
            || $this->phpAttributeAnalyzer->hasPhpAttribute($classMethod, self::SCOPE_ATTRIBUTE);
    }

    private function hasBuilderFirstParameter(ClassMethod $classMethod): bool
    {
        if ($classMethod->params === []) {
            return false;
        }

        $firstParam = $classMethod->params[0];

        if ($firstParam->type === null) {
            return true;
        }

        return $this->nodeTypeResolver->isObjectType($firstParam->type, new ObjectType(self::ELOQUENT_BUILDER));
    }

    private function hasScopeReturnType(ClassMethod $classMethod): bool
    {
        if ($classMethod->returnType === null) {
            return true;
        }

        if ($classMethod->returnType instanceof Identifier && $classMethod->returnType->toString() === 'void') {
            return true;
        }

        return $this->nodeTypeResolver->isObjectType($classMethod->returnType, new ObjectType(self::ELOQUENT_BUILDER));
    }
}

<?php

namespace RectorLaravel\NodeAnalyzer;

use InvalidArgumentException;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class RelationshipAnalyzer
{
    protected static function relationType(): ObjectType
    {
        return new ObjectType('Illuminate\Database\Eloquent\Relations\Relation');
    }

    protected static function modelType(): ObjectType
    {
        return new ObjectType('Illuminate\Database\Eloquent\Model');
    }

    /**
     * Resolve the Related Model of the Relationship.
     *
     * @return ObjectType|null
     */
    public function resolveRelatedForRelation(Type $objectType, Scope $scope): ?Type
    {
        if ($objectType->isObject()->no() || $objectType->isSuperTypeOf(self::relationType())->no()) {
            throw new InvalidArgumentException('Object type must be an Eloquent relation.');
        }

        $extendedPropertyReflection = $objectType->getInstanceProperty('related', $scope);
        $modelType = $extendedPropertyReflection->getReadableType();

        if ($modelType->isSuperTypeOf(self::modelType())->no()) {
            return null;
        }

        /** @phpstan-ignore return.type */
        return $modelType;
    }

    /**
     * Resolve the Parent Model of the Relationship.
     *
     * @return ObjectType|null
     */
    public function resolveParentForRelation(Type $objectType, Scope $scope): ?Type
    {
        if ($objectType->isObject()->no() || $objectType->isSuperTypeOf(self::relationType())->no()) {
            throw new InvalidArgumentException('Object type must be an Eloquent relation.');
        }

        $extendedPropertyReflection = $objectType->getInstanceProperty('parent', $scope);
        $modelType = $extendedPropertyReflection->getReadableType();

        if ($modelType->isSuperTypeOf(self::modelType())->no()) {
            return null;
        }

        /** @phpstan-ignore return.type */
        return $modelType;
    }
}

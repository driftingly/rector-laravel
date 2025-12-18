<?php

namespace RectorLaravel\NodeAnalyzer;

use Illuminate\Database\Eloquent\Relations\Relation;
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
    public function resolveRelatedForRelation(Type $objectType): ?Type
    {
        $modelType = $objectType->getTemplateType(Relation::class, 'TRelatedModel');
        if (self::modelType()->isSuperTypeOf($modelType)->no()) {
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
    public function resolveParentForRelation(Type $objectType): ?Type
    {
        $modelType = $objectType->getTemplateType(Relation::class, 'TDeclaringModel');

        if (self::modelType()->isSuperTypeOf($modelType)->no()) {
            return null;
        }

        /** @phpstan-ignore return.type */
        return $modelType;
    }
}

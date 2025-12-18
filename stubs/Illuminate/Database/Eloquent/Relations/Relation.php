<?php

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Contracts\Database\Eloquent\Builder;

if (class_exists('Illuminate\Database\Eloquent\Relations\Relation')) {
    return;
}

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 * @template TResult
 *
 * @mixin \Illuminate\Database\Eloquent\Builder<TRelatedModel>
 */
abstract class Relation
{
    /**
     * An array to map morph names to their class names in the database.
     *
     * @var array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    public static $morphMap = [];

    /**
     * Indicates if the relation is adding constraints.
     *
     * @var bool
     */
    protected static $constraints = true;

    /**
     * Prevents morph relationships without a morph map.
     *
     * @var bool
     */
    protected static $requireMorphMap = false;

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * The Eloquent query builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder<TRelatedModel>
     */
    protected $query;

    /**
     * The parent model instance.
     *
     * @var TDeclaringModel
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var TRelatedModel
     */
    protected $related;

    /**
     * Indicates whether the eagerly loaded relation should implicitly return an empty collection.
     *
     * @var bool
     */
    protected $eagerKeysWereEmpty = false;
}

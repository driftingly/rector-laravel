<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\BelongsToMany')) {
    return;
}

/**
 * The pivot model is bound to Model rather than Pivot, as the stubs have no Pivot class.
 *
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 * @template TPivotModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 *
 * @extends Relation<TRelatedModel, TDeclaringModel, mixed>
 */
class BelongsToMany extends Relation
{
    /**
     * Specify the custom pivot model to use for the relationship.
     *
     * @template TNewPivotModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TNewPivotModel>  $class
     * @return $this
     *
     * @phpstan-this-out static<TRelatedModel, TDeclaringModel, TNewPivotModel>
     */
    public function using($class) {}
}

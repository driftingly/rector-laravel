<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\MorphToMany')) {
    return;
}

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 * @template TPivotModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 *
 * @extends BelongsToMany<TRelatedModel, TDeclaringModel, TPivotModel>
 */
class MorphToMany extends BelongsToMany {}

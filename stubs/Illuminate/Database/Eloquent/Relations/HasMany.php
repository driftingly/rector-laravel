<?php

namespace Illuminate\Database\Eloquent\Relations;

if (class_exists('Illuminate\Database\Eloquent\Relations\HasMany')) {
    return;
}

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Relations\HasOneOrMany<TRelatedModel, TDeclaringModel, \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>>
 */
class HasMany extends HasOneOrMany {}

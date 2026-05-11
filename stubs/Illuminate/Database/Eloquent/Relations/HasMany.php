<?php

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Collection;

if (class_exists('Illuminate\Database\Eloquent\Relations\HasMany')) {
    return;
}

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends HasOneOrMany<TRelatedModel, TDeclaringModel, Collection<int, TRelatedModel>>
 */
class HasMany extends HasOneOrMany {}

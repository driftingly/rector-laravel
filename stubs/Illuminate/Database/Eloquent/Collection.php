<?php

declare(strict_types=1);

namespace Illuminate\Database\Eloquent;

if (class_exists('Illuminate\Database\Eloquent\Collection')) {
    return;
}

/**
 * @template TKey of array-key
 * @template TModel of Model
 *
 * @extends \Illuminate\Support\Collection<TKey, TModel>
 */
class Collection extends \Illuminate\Support\Collection {}

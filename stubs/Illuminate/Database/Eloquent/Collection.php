<?php

declare(strict_types=1);

namespace Illuminate\Database\Eloquent;

use Illuminate\Support\Collection as BaseCollection;

if (class_exists('Illuminate\Database\Eloquent\Collection')) {
    return;
}

/**
 * @template TKey of array-key
 * @template TModel of Model
 *
 * @extends BaseCollection<TKey, TModel>
 */
class Collection extends BaseCollection {}

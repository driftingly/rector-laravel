<?php

declare(strict_types=1);

namespace Illuminate\Support;

if (class_exists('Illuminate\Support\Collection')) {
    return;
}

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @implements Enumerable<TKey, TValue>
 */
class Collection implements Enumerable {}

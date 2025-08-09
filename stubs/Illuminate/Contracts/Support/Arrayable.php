<?php

declare(strict_types=1);

namespace Illuminate\Contracts\Support;

if (interface_exists('Illuminate\Contracts\Support\Arrayable')) {
    return;
}

/**
 * @template TKey of array-key
 * @template TValue
 */
interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray();
}

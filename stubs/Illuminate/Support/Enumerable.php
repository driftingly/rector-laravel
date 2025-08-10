<?php

declare(strict_types=1);

namespace Illuminate\Support;

if (class_exists('Illuminate\Support\Enumerable')) {
    return;
}

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends \Illuminate\Contracts\Support\Arrayable<TKey, TValue>
 * @extends \IteratorAggregate<TKey, TValue>
 */
interface Enumerable
{
    /**
     * Alias for the "avg" method.
     *
     * @param  (callable(TValue): float|int)|string|null  $callback
     * @return float|int|null
     */
    public function average($callback = null);

    /**
     * Get the average value of a given key.
     *
     * @param  (callable(TValue): float|int)|string|null  $callback
     * @return float|int|null
     */
    public function avg($callback = null);

    /**
     * Alias for the "contains" method.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function some($key, $operator = null, $value = null);

    /**
     * Determine if an item exists in the enumerable.
     *
     * @param  (callable(TValue, TKey): bool)|TValue|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null);

    /**
     * Determine if an item is not contained in the collection.
     *
     * @param  mixed  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function doesntContain($key, $operator = null, $value = null);

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue): bool)|null  $callback
     * @return static
     */
    public function filter(?callable $callback = null);

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param  (callable(TValue, TKey): bool)|bool|TValue  $callback
     * @return static
     */
    public function reject($callback = true);

    /**
     * Apply the callback unless the collection is empty.
     *
     * @template TUnlessEmptyReturnType
     *
     * @param  callable($this): TUnlessEmptyReturnType  $callback
     * @param  (callable($this): TUnlessEmptyReturnType)|null  $default
     * @return $this|TUnlessEmptyReturnType
     */
    public function unlessEmpty();

    /**
     * Apply the callback unless the collection is not empty.
     *
     * @template TUnlessNotEmptyReturnType
     *
     * @param  callable($this): TUnlessNotEmptyReturnType  $callback
     * @param  (callable($this): TUnlessNotEmptyReturnType)|null  $default
     * @return $this|TUnlessNotEmptyReturnType
     */
    public function unlessNotEmpty();

    /**
     * Get the collection of items as a plain array.
     *
     * @return array<TKey, mixed>
     */
    public function toArray();

    /**
     * Get all items in the enumerable.
     *
     * @return array<TKey, TValue>
     */
    public function all();
}

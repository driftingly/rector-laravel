<?php

namespace Illuminate\Database\Query;

if (class_exists('Illuminate\Database\Query\Builder')) {
    return;
}

class Builder implements \Illuminate\Contracts\Database\Query\Builder
{
    public function publicMethodBelongsToQueryBuilder(): void {}

    /**
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orderBy($column, string $direction): static {}

    /**
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return $this
     */
    public function orderByDesc($column): static {}

    /**
     * @param  \Illuminate\Contracts\Database\Query\Expression|string  $column
     * @return mixed
     */
    public function max($column) {}

    /**
     * Add a "where null" clause to the query.
     *
     * @param  string|array|\Illuminate\Contracts\Database\Query\Expression  $columns
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereNull($columns, $boolean = 'and', $not = false) {}

    protected function protectedMethodBelongsToQueryBuilder(): void {}

    private function privateMethodBelongsToQueryBuilder(): void {}
}

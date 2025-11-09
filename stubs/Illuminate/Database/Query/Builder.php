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

    protected function protectedMethodBelongsToQueryBuilder(): void {}

    private function privateMethodBelongsToQueryBuilder(): void {}
}

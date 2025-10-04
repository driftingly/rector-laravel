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
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and'): static
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function whereNot($column, $operator = null, $value = null, $boolean = 'and'): static
    {
        return $this;
    }

    protected function protectedMethodBelongsToQueryBuilder(): void {}

    private function privateMethodBelongsToQueryBuilder(): void {}
}

<?php

namespace Illuminate\Database\Query;

use Closure;
use Illuminate\Contracts\Database\Query\Expression;

if (class_exists('Illuminate\Database\Query\Builder')) {
    return;
}

class Builder implements \Illuminate\Contracts\Database\Query\Builder
{
    public function publicMethodBelongsToQueryBuilder(): void {}

    /**
     * Add a basic where clause to the query.
     *
     * @param  Closure|string|array|Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  Closure|string|array|Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this;
    }

    protected function protectedMethodBelongsToQueryBuilder(): void {}

    private function privateMethodBelongsToQueryBuilder(): void {}
}

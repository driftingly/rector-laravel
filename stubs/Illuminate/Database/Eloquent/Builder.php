<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;

if (class_exists('Illuminate\Database\Eloquent\Builder')) {
    return;
}

class Builder extends QueryBuilder
{
    public function publicMethodBelongsToEloquentQueryBuilder(): void {}

    public function excludablePublicMethodBelongsToEloquentQueryBuilder(): void {}

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
}

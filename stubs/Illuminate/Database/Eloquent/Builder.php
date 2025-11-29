<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;

if (class_exists('Illuminate\Database\Eloquent\Builder')) {
    return;
}

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @mixin \Illuminate\Database\Query\Builder
 */
class Builder extends QueryBuilder implements \Illuminate\Contracts\Database\Eloquent\Builder
{
    /**
     * The model being queried.
     *
     * @var TModel
     */
    protected $model;

    public function publicMethodBelongsToEloquentQueryBuilder(): void {}

    public function excludablePublicMethodBelongsToEloquentQueryBuilder(): void {}
}

<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;

if (class_exists('Illuminate\Database\Eloquent\Builder')) {
    return;
}

class Builder extends QueryBuilder
{
    public function publicMethodBelongsToEloquentQueryBuilder(): void
    {
    }

    public function excludablePublicMethodBelongsToEloquentQueryBuilder(): void
    {
    }
}

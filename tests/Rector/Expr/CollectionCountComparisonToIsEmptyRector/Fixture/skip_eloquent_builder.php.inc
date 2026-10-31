<?php

namespace RectorLaravel\Tests\Rector\Expr\CollectionCountComparisonToIsEmptyRector\Fixture;

use Illuminate\Database\Eloquent\Builder;

function skipEloquentBuilder(Builder $query)
{
    return $query->count() > 0;
}

?>

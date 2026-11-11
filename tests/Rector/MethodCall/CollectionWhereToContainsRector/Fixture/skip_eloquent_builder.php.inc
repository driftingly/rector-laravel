<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Database\Eloquent\Builder;

function skipEloquentBuilder(Builder $query)
{
    $query->where('status', 'open')->isEmpty();
    $query->where('status', 'open')->isNotEmpty();
}

?>

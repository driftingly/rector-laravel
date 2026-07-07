<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Builder;

class NotAModel
{
    public function scopeFoo(Builder $query): Builder
    {
        return $query->where(...);
    }
}

?>

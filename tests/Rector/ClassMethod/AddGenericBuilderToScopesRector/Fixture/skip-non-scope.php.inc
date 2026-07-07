<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    public function getActivePosts(Builder $query): Builder
    {
        return $this->query->where(...);
    }
}

?>

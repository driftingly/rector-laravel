<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Post> $query
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->where('votes', '>', 100);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Post> $query
     * @return \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Post>
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->where('votes', '>', 100);
    }
}

?>

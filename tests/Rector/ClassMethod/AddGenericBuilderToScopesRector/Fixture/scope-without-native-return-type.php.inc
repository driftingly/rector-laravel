<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    public function scopeApproved(Builder $query)
    {
        return $query->where('approved', true);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\Comment> $query
     */
    public function scopeApproved(Builder $query)
    {
        return $query->where('approved', true);
    }
}

?>

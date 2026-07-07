<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class User extends Model
{
    #[Scope]
    public function popular(Builder $query): Builder
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
use Illuminate\Database\Eloquent\Attributes\Scope;

class User extends Model
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\User> $query
     * @return \Illuminate\Database\Eloquent\Builder<\RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture\User>
     */
    #[Scope]
    public function popular(Builder $query): Builder
    {
        return $query->where('votes', '>', 100);
    }
}

?>

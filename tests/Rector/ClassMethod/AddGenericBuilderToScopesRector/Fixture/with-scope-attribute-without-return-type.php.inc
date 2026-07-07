<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class User extends Model
{
    #[Scope]
    public function popular(Builder $query)
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
     * @param \Illuminate\Database\Eloquent\Builder<static> $query
     */
    #[Scope]
    public function popular(Builder $query)
    {
        return $query->where('votes', '>', 100);
    }
}

?>

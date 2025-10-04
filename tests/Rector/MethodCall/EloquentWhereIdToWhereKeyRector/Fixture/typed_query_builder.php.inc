<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class TypedQueryBuilder
{
    public function run($user)
    {
        /** @var \Illuminate\Contracts\Database\Eloquent\Builder $query */
        $query = User::query();

        $query->where('id', '=', $user->id)->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class TypedQueryBuilder
{
    public function run($user)
    {
        /** @var \Illuminate\Contracts\Database\Eloquent\Builder $query */
        $query = User::query();

        $query->whereKey($user)->get();
    }
}

?>

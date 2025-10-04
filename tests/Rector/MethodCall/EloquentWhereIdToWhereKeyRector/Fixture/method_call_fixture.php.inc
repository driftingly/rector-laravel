<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class MethodCallFixture
{
    public function run($user)
    {
        /** @var \Illuminate\Contracts\Database\Query\Builder $query */
        $query = User::query();

        $query->where('id', '=', $user->id)->get();
        $query->where('id', '!=', $user->id)->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class MethodCallFixture
{
    public function run($user)
    {
        /** @var \Illuminate\Contracts\Database\Query\Builder $query */
        $query = User::query();

        $query->whereKey($user)->get();
        $query->whereKeyNot($user)->get();
    }
}

?>

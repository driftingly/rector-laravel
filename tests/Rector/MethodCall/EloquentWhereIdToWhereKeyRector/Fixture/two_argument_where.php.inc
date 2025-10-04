<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class TwoArgumentWhere
{
    public function run($user)
    {
        // 2-argument where() with id
        User::where('id', $user->id)->get();

        // 2-argument whereNot() with id
        User::whereNot('id', $user->id)->get();

        // Chained with 2-argument
        $query = User::query()
            ->where('name', 'John')
            ->where('id', $user->id)
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class TwoArgumentWhere
{
    public function run($user)
    {
        // 2-argument where() with id
        User::whereKey($user)->get();

        // 2-argument whereNot() with id
        User::whereKeyNot($user)->get();

        // Chained with 2-argument
        $query = User::query()
            ->where('name', 'John')->whereKey($user)
            ->get();
    }
}

?>

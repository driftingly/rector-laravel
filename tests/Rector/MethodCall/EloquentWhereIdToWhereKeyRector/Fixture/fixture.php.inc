<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class SomeClass
{
    public function run($user)
    {
        User::where('id', '=', $user->id)->get();
        User::where('id', '!=', $user->id)->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class SomeClass
{
    public function run($user)
    {
        User::whereKey($user)->get();
        User::whereKeyNot($user)->get();
    }
}

?>

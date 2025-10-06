<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class ChainedMethodsFixture
{
    public function run($user)
    {
        User::where('id', '=', $user->id)->where('status', 'active')->get();

        $query = User::query()
            ->where('name', 'John')
            ->where('id', '=', $user->id)
            ->where('email', 'LIKE', '%@example.com');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class ChainedMethodsFixture
{
    public function run($user)
    {
        User::whereKey($user)->where('status', 'active')->get();

        $query = User::query()
            ->where('name', 'John')->whereKey($user)
            ->where('email', 'LIKE', '%@example.com');
    }
}

?>

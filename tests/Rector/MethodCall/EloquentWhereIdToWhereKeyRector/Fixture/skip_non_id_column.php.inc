<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\Source\User;

class SkipNonIdColumn
{
    public function run($user)
    {
        // Should NOT transform - different column name
        User::where('name', '=', $user->id)->get();
        User::where('name', $user->id)->get();

        // Should NOT transform - different property
        User::where('id', '=', $user->name)->get();
        User::where('id', $user->name)->get();

        // Should NOT transform - different operator
        User::where('id', '>', $user->id)->get();
        User::where('id', '<', $user->id)->get();

        // Should NOT transform - not a property fetch
        User::where('id', '=', 123)->get();
        User::where('id', 123)->get();
    }
}

?>

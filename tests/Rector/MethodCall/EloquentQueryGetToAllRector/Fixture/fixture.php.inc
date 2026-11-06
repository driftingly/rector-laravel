<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source\User;

User::query()->get();
User::query()->get(['id', 'name']);
User::query()->get(columns: ['id']);
$count = User::query()->get()->count();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source\User;

User::all();
User::all(['id', 'name']);
User::all(columns: ['id']);
$count = User::all()->count();

?>

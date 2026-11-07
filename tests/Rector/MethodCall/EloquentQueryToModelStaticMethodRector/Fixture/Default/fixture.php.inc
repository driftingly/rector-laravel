<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Fixture\Default;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\User;
use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\UserWithCustomQuery;

User::query()->get();
User::query()->get(['id', 'name']);
User::query()->get(columns: ['id']);
User::query()->with('posts')->where('active', true)->get();
User::query()->useWritePdo()->find(1);
UserWithCustomQuery::query()->get();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Fixture\Default;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\User;
use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\UserWithCustomQuery;

User::all();
User::all(['id', 'name']);
User::all(columns: ['id']);
User::with('posts')->where('active', true)->get();
User::onWriteConnection()->find(1);
UserWithCustomQuery::all();

?>

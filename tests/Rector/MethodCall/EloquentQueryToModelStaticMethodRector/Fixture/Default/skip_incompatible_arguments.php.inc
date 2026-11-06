<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Fixture\Default;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\User;

User::query()->with('posts', fn ($query) => $query->latest());
User::query()->with(relations: 'posts', callback: fn ($query) => $query->latest());
User::query()->get(...$columns);
User::query()->get(...);

?>

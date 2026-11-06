<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Fixture\Default;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\User;

User::query()->where('active', true)->get();
User::query()->first();
User::where('active', true)->get();

?>

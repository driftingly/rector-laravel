<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source\User;

User::query()->where('active', true)->get();
User::query()->first();
User::where('active', true)->get();

?>

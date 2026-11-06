<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source\UserWithCustomAll;
use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source\UserWithCustomQuery;

UserWithCustomAll::query()->get();
UserWithCustomQuery::query()->get();

?>

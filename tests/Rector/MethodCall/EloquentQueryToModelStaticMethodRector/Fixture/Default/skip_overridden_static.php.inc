<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Fixture\Default;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\UserInheritingCustomAll;
use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\UserWithCustomAll;

UserWithCustomAll::query()->get();
UserInheritingCustomAll::query()->get();

?>

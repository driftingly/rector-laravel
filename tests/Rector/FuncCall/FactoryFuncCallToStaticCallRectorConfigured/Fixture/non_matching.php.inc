<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\Fixture;

use App\User;

factory(User::class);
factory(User::class, 1);

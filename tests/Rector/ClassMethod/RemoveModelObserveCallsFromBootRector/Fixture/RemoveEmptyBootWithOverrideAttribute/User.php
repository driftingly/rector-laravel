<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveEmptyBootWithOverrideAttribute;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
}

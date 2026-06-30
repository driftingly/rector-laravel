<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveDirectObserveWhenModelAlreadyHasMatchingAttribute;

use Illuminate\Foundation\Auth\User as Authenticatable;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveDirectObserveWhenModelAlreadyHasMatchingAttribute\UserObserver::class])]
class User extends Authenticatable
{
}

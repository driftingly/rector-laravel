<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\SimpleObserveRegistration;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
}

?>
-----
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\SimpleObserveRegistration;

use Illuminate\Foundation\Auth\User as Authenticatable;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\SimpleObserveRegistration\UserObserver::class])]
class User extends Authenticatable
{
}

?>

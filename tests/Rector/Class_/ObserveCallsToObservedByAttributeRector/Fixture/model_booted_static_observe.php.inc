<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\ModelBootedStaticObserve;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public static function booted(): void
    {
        static::observe(UserObserver::class);
    }
}

final class UserObserver
{
}

?>
-----
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\ModelBootedStaticObserve;

use Illuminate\Foundation\Auth\User as Authenticatable;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\ModelBootedStaticObserve\UserObserver::class])]
class User extends Authenticatable
{
    public static function booted(): void
    {
        static::observe(UserObserver::class);
    }
}

final class UserObserver
{
}

?>

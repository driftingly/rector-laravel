<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveModelBootedStaticObserve;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    #[\Override]
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

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveModelBootedStaticObserve;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
}

final class UserObserver
{
}

?>

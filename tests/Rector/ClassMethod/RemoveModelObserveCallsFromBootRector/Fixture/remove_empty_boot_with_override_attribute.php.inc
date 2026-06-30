<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveEmptyBootWithOverrideAttribute;

final class AppServiceProvider
{
    #[\Override]
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}

final class UserObserver
{
}

?>
-----
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveEmptyBootWithOverrideAttribute;

final class AppServiceProvider
{
}

final class UserObserver
{
}

?>

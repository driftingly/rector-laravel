<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveDirectObserveWhenModelAlreadyHasMatchingAttribute;

final class AppServiceProvider
{
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

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveDirectObserveWhenModelAlreadyHasMatchingAttribute;

final class AppServiceProvider
{
    public function boot(): void
    {
    }
}

final class UserObserver
{
}

?>

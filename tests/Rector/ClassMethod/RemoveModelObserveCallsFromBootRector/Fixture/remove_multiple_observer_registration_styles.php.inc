<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveMultipleObserverRegistrationStyles;

final class AppServiceProvider
{
    public function boot(): void
    {
        User::observe([FirstObserver::class, SecondObserver::class]);
        Post::observe(PostObserver::class);
    }
}

final class FirstObserver
{
}

final class SecondObserver
{
}

final class PostObserver
{
}

?>
-----
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveMultipleObserverRegistrationStyles;

final class AppServiceProvider
{
}

final class FirstObserver
{
}

final class SecondObserver
{
}

final class PostObserver
{
}

?>

<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\SkipNestedObserveCall;

final class UserObserver
{
}

final class AppServiceProvider
{
    public function boot(): void
    {
        if (true) {
            User::observe(UserObserver::class);
        }
    }
}

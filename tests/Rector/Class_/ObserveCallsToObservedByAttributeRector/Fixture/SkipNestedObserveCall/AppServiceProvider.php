<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\SkipNestedObserveCall;

final class AppServiceProvider
{
    public function boot(): void
    {
        if (rand(0, 1) === 1) {
            User::observe(UserObserver::class);
        }
    }
}

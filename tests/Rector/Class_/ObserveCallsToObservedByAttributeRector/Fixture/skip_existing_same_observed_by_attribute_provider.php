<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\SkipExistingSameObservedByAttribute;

final class UserObserver
{
}

final class AppServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture\ObservedByAttribute;

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

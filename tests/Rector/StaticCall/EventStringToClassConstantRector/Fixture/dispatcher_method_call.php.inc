<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

function boot(\Illuminate\Contracts\Events\Dispatcher $events)
{
    $events->listen('auth.login', function () {});
    $events->dispatch('auth.logout');
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

function boot(\Illuminate\Contracts\Events\Dispatcher $events)
{
    $events->listen(\Illuminate\Auth\Events\Login::class, function () {});
    $events->dispatch(\Illuminate\Auth\Events\Logout::class);
}

?>

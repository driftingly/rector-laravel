<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

\Illuminate\Support\Facades\Event::listen('auth.login', function () {});
\Illuminate\Support\Facades\Event::listen(['auth.login', 'auth.logout'], function () {});
\Illuminate\Support\Facades\Event::fire('auth.login');
\Illuminate\Support\Facades\Event::forget('auth.logout');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

\Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function () {});
\Illuminate\Support\Facades\Event::listen([\Illuminate\Auth\Events\Login::class, \Illuminate\Auth\Events\Logout::class], function () {});
\Illuminate\Support\Facades\Event::fire(\Illuminate\Auth\Events\Login::class);
\Illuminate\Support\Facades\Event::forget(\Illuminate\Auth\Events\Logout::class);

?>

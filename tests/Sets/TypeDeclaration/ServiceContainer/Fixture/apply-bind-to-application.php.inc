<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\ServiceContainer\Fixture;

\Illuminate\Support\Facades\App::bind('class', function ($app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::bindIf('class', function ($app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::singleton('class', function ($app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::singletonIf('class', function ($app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::scoped('class', function ($app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::scopedIf('class', function ($app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::extends(\Illuminate\Support\Collection::class, function ($instance, $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::resolving(\Illuminate\Support\Collection::class, function ($instance, $app) {
    return new \stdClass();
});

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\ServiceContainer\Fixture;

\Illuminate\Support\Facades\App::bind('class', function (\Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::bindIf('class', function (\Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::singleton('class', function (\Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::singletonIf('class', function (\Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::scoped('class', function (\Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::scopedIf('class', function (\Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::extends(\Illuminate\Support\Collection::class, function (\Illuminate\Support\Collection $instance, \Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

\Illuminate\Support\Facades\App::resolving(\Illuminate\Support\Collection::class, function (\Illuminate\Support\Collection $instance, \Illuminate\Contracts\Foundation\Application $app) {
    return new \stdClass();
});

?>

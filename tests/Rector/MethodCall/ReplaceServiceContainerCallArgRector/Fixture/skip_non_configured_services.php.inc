<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\Fixture;

function foo(\Illuminate\Contracts\Container\Container $app) {
    $app->make('foobar')->encrypt('hello world');
}

\Illuminate\Support\Facades\Application::make('foobar')->encrypt('hello world');

app('foobar')->encrypt('foobar');

?>

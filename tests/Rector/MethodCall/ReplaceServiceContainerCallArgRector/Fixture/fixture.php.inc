<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\Fixture;

function foo(\Illuminate\Contracts\Container\Container $app) {
    $app->make('encrypter')->encrypt('hello world');
}

\Illuminate\Support\Facades\Application::make('encrypter')->encrypt('hello world');

app('encrypter')->encrypt('hello world');

function foo(\Illuminate\Contracts\Container\Container $app) {
    $app->make(\Illuminate\Contracts\Session\Session::class)->get('hello world');
}

\Illuminate\Support\Facades\Application::make(\Illuminate\Contracts\Session\Session::class)->get('hello world');

app(\Illuminate\Contracts\Session\Session::class)->get('hello world');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\Fixture;

function foo(\Illuminate\Contracts\Container\Container $app) {
    $app->make(\Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('hello world');
}

\Illuminate\Support\Facades\Application::make(\Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('hello world');

app(\Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('hello world');

function foo(\Illuminate\Contracts\Container\Container $app) {
    $app->make('session')->get('hello world');
}

\Illuminate\Support\Facades\Application::make('session')->get('hello world');

app('session')->get('hello world');

?>

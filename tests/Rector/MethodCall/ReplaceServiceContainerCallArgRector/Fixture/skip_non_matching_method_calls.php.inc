<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\Fixture;

function foo (\Illuminate\Contracts\Container\Container $app) {
    $app->build('encrypter')->encrypt('hello world');
}

\Illuminate\Support\Facades\Application::build('encrypter')->encrypt('hello world');

?>

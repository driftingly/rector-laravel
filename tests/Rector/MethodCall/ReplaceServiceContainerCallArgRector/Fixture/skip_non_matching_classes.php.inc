<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\Fixture;

function foo ($app) {
    $app->make('encrypter')->encrypt('hello world');
}

Application::make('encrypter')->encrypt('hello world');

?>

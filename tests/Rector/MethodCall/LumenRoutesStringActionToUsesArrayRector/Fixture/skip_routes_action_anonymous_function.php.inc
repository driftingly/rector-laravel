<?php

namespace RectorLaravel\Tests\Rector\MethodCall\LumenRoutesStringActionToUsesArrayRector\Fixture;

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('foo', function () {
    return 'Hello World';
});

?>

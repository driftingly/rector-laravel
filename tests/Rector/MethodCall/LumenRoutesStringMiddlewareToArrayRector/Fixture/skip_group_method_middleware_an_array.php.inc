<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector\Fixture;

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->get('/', 'HomeController@home');
});

?>

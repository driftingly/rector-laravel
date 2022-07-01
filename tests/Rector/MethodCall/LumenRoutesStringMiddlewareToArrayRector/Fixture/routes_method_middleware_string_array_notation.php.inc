<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector\Fixture;

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['middleware' => 'first|second'], function () use ($router) {
    $router->get('/', ['uses' => 'HomeController@home', 'middleware' => 'third|fourth']);
});

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector\Fixture;

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['middleware' => ['first', 'second']], function () use ($router) {
    $router->get('/', ['uses' => 'HomeController@home', 'middleware' => ['third', 'fourth']]);
});

?>

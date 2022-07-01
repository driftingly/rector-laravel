<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector\Fixture;

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', ['uses' => 'HomeController@home', 'middleware' => ['auth']]);
$router->post('/', ['uses' => 'HomeController@home', 'middleware' => ['auth']]);
$router->put('/', ['uses' => 'HomeController@home', 'middleware' => ['auth']]);
$router->delete('/', ['uses' => 'HomeController@home', 'middleware' => ['auth']]);
$router->options('/', ['uses' => 'HomeController@home', 'middleware' => ['auth']]);
$router->patch('/', ['uses' => 'HomeController@home', 'middleware' => ['auth']]);

?>

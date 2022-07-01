<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\LumenRoutesStringActionToUsesArrayRector\Fixture;

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', 'HomeController@home');

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\LumenRoutesStringActionToUsesArrayRector\Fixture;

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', ['uses' => 'HomeController@home']);

?>

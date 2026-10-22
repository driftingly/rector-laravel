<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ContainerArrayAccessToMethodCallRector\Fixture;

$container = new \Illuminate\Container\Container();
$container['foo'];
$container['foo'] = 'Foo';
isset($container['foo']);
unset($container['foo']); // skipped, no unset method

$app = new \Illuminate\Foundation\Application();
$app['foo'];
$app['foo'] = 'Foo';
isset($app['foo']);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ContainerArrayAccessToMethodCallRector\Fixture;

$container = new \Illuminate\Container\Container();
$container->make('foo');
$container->bind('foo', 'Foo');
$container->bound('foo');
unset($container['foo']); // skipped, no unset method

$app = new \Illuminate\Foundation\Application();
$app->make('foo');
$app->bind('foo', 'Foo');
$app->bound('foo');

?>

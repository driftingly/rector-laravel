<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RepositoryArrayAccessToMethodCallRector\Fixture;

$config = new \Illuminate\Config\Repository();
$config['app.name'];
$config['app.name'] = 'Foo';
isset($config['app.name']);
unset($config['app.name']);

$cache = new \Illuminate\Cache\Repository();
$cache['app.name'];
$cache['app.name'] = 'Foo';
isset($cache['app.name']);
unset($cache['app.name']);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RepositoryArrayAccessToMethodCallRector\Fixture;

$config = new \Illuminate\Config\Repository();
$config->get('app.name');
$config->set('app.name', 'Foo');
$config->has('app.name');
$config->set('app.name');

$cache = new \Illuminate\Cache\Repository();
$cache->get('app.name');
$cache->set('app.name', 'Foo');
$cache->has('app.name');
$cache->forget('app.name');

?>

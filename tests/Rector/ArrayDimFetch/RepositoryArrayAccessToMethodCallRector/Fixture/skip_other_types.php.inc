<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RepositoryArrayAccessToMethodCallRector\Fixture;

$array = ['app.name' => 'Foo'];
$array['app.name'];
$array['app.name'] = 'Foo';
isset($array['app.name']);
unset($array['app.name']);

$container = new \Illuminate\Container\Container();
$container['foo'];
$container['foo'] = 'Foo';
isset($container['foo']);
unset($container['foo']);

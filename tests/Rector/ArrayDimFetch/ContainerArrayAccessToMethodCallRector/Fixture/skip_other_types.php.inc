<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ContainerArrayAccessToMethodCallRector\Fixture;

$array = ['foo' => 'Foo'];
$array['foo'];
$array['foo'] = 'Foo';
isset($array['foo']);

$config = new \Illuminate\Config\Repository();
$config['app.name'];

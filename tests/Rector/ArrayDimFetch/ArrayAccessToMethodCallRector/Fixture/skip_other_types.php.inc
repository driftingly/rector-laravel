<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayAccessToMethodCallRector\Fixture;

$array = ['app.name' => 'Foo'];
$array['app.name'];
$array['app.name'] = 'Foo';
isset($array['app.name']);
unset($array['app.name']);

<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AppToResolveRector\Fixture;

app('foo');
app('bar', ['foo' => 'bar']);
app('baz')->foo();
app('baq')();
app('baq')(5);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AppToResolveRector\Fixture;

resolve('foo');
resolve('bar', ['foo' => 'bar']);
resolve('baz')->foo();
resolve('baq')();
resolve('baq')(5);

?>

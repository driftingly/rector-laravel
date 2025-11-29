<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AppToResolveRector\Fixture;

app('foo');
app('bar', ['foo' => 'bar']);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AppToResolveRector\Fixture;

resolve('foo');
resolve('bar', ['foo' => 'bar']);

?>

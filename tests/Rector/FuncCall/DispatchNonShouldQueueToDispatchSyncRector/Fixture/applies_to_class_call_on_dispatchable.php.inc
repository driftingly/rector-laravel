<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\ClassWithDispatchableTrait;
use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

$a = (new ClassWithDispatchableTrait())->dispatch(new SomeJob());

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\ClassWithDispatchableTrait;
use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

$a = (new ClassWithDispatchableTrait())->dispatchSync(new SomeJob());

?>

<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

$a = dispatch(new SomeJob());

if (dispatch(new SomeJob())) {
    echo 'hi';
}

if (dispatch($b = new SomeJob())) {
    echo 'hi';
}

$job = new SomeJob();
$c = dispatch($job);

foreach (dispatch($job) as $x) {
    echo $x;
}

$d = dispatch(dispatch(new SomeJob()));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

$a = dispatch_sync(new SomeJob());

if (dispatch_sync(new SomeJob())) {
    echo 'hi';
}

if (dispatch_sync($b = new SomeJob())) {
    echo 'hi';
}

$job = new SomeJob();
$c = dispatch_sync($job);

foreach (dispatch_sync($job) as $x) {
    echo $x;
}

$d = dispatch(dispatch_sync(new SomeJob()));

?>

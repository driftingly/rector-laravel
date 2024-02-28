<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

\Illuminate\Support\Facades\Bus::dispatch(new SomeJob());

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

\Illuminate\Support\Facades\Bus::dispatchSync(new SomeJob());

?>

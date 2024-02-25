<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

class SomeClass
{
    public function run(\Illuminate\Contracts\Bus\Dispatcher $dispatcher)
    {
        $dispatcher->dispatch(new SomeJob());
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source\SomeJob;

class SomeClass
{
    public function run(\Illuminate\Contracts\Bus\Dispatcher $dispatcher)
    {
        $dispatcher->dispatchSync(new SomeJob());
    }
}

?>

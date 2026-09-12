<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Bus\Dispatchable;

abstract class AbstractBusDispatchableJob
{
    use Dispatchable;

    public function retryLater()
    {
        self::dispatch();
        static::dispatch();
        self::dispatchSync();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Bus\Dispatchable;

abstract class AbstractBusDispatchableJob
{
    use Dispatchable;

    public function retryLater()
    {
        dispatch(new static());
        dispatch(new static());
        dispatch_sync(new static());
    }
}

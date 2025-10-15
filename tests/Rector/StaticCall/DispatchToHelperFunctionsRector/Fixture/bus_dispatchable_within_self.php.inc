<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Bus\Dispatchable;

class BusDispatchableWithinSelf
{
    use Dispatchable;

    public function handle()
    {
        self::dispatch();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Bus\Dispatchable;

class BusDispatchableWithinSelf
{
    use Dispatchable;

    public function handle()
    {
        dispatch(new self());
    }
}

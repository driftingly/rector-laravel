<?php

namespace RectorLaravel\Tests\Sets\Laravel100;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Bus;

class DispatchableJobClass
{
    use Dispatchable;
}

class DispatchesJobsClass
{
    use DispatchesJobs;
}

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    protected $routeMiddleware = [];
}

Bus::dispatchNow(fn () => null);
DispatchableJobClass::dispatchNow(fn () => null);
DispatchesJobsClass::dispatchNow(fn () => null);
dispatch_now(fn () => null);

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel100;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Bus;

class DispatchableJobClass
{
    use Dispatchable;
}

class DispatchesJobsClass
{
    use DispatchesJobs;
}

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    protected $middlewareAliases = [];
}

Bus::dispatchSync(fn () => null);
DispatchableJobClass::dispatchSync(fn () => null);
DispatchesJobsClass::dispatchSync(fn () => null);
dispatch_sync(fn () => null);

?>

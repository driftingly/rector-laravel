<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Events\Dispatchable;

class FixtureEvent
{
    use Dispatchable;

    public function __construct(
        private string $param1,
        private string $param2,
    ) {
    }
}

FixtureEvent::dispatch('param1', 'param2');
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Events\Dispatchable;

class FixtureEvent
{
    use Dispatchable;

    public function __construct(
        private string $param1,
        private string $param2,
    ) {
    }
}

event(new RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture\FixtureEvent('param1', 'param2'));

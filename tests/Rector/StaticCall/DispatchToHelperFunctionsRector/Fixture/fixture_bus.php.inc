<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Bus\Dispatchable;

class FixtureBus
{
    use Dispatchable;

    public function __construct(
        private string $param1,
        private string $param2,
    ) {
    }
}

FixtureBus::dispatch('param1', 'param2');
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Bus\Dispatchable;

class FixtureBus
{
    use Dispatchable;

    public function __construct(
        private string $param1,
        private string $param2,
    ) {
    }
}

dispatch(new RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture\FixtureBus('param1', 'param2'));

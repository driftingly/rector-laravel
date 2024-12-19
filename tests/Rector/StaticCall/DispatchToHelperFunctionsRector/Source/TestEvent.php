<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source;

use Illuminate\Foundation\Events\Dispatchable;

class TestEvent
{
    use Dispatchable;

    public function __construct(
        private string $param1,
        private string $param2,
    ) {}
}

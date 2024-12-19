<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Source;

use Some\Other\Dispatchable;

class OtherDispatchable
{
    use Dispatchable;

    public function __construct(
        private string $param1,
        private string $param2,
    ) {}
}

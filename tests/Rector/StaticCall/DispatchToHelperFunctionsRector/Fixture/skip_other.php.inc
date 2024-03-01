<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Some\Other\Dispatchable;

class SkipOther
{
    use Dispatchable;

    public function __construct(
        private string $param1,
        private string $param2,
    ) {
    }
}

SkipOther::dispatch('param1', 'param2');

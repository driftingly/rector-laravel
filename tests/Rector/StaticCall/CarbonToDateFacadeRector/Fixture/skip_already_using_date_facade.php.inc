<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use Illuminate\Support\Facades\Date;

class SkipAlreadyUsingDateFacade
{
    public function run()
    {
        // Should not be changed - already using Date facade
        $now = Date::now();
    }
}

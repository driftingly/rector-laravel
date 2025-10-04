<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use SomeOther\Carbon;

class SkipNonCarbonClass
{
    public function run()
    {
        // Should not be changed - different Carbon class
        $now = Carbon::now();
    }
}

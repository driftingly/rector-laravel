<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Carbon\Carbon;

class SkipInStaticMethodTest extends TestCase
{
    public static function validationDataProvider(): array
    {
        Carbon::setTestNow('2025-10-14');

        return [];
    }
}

?>

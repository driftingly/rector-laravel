<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Carbon\Carbon;

class SkipInStaticClosureTest extends TestCase
{
    public function test()
    {
        $callback = static function () {
            Carbon::setTestNow('2025-10-14');
        };

        $callback();
    }
}

?>

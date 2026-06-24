<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Testing\TestCase;

/**
 * @param-closure-this TestCase $closure
 */
function runTest(Closure $closure): void
{
    $closure();
}

runTest(function () {
    Carbon::setTestNow('2024-08-11');
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Testing\TestCase;

/**
 * @param-closure-this TestCase $closure
 */
function runTest(Closure $closure): void
{
    $closure();
}

runTest(function () {
    $this->travelTo('2024-08-11');
});

?>

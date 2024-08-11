<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Foo\TestCase;
use Carbon\Carbon;

class SomeTest extends TestCase
{
    public function test()
    {
        Carbon::setTestNow('2024-08-11');
        Carbon::setTestNow(Carbon::parse('2024-08-11'));
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Foo\TestCase;
use Carbon\Carbon;

class SomeTest extends TestCase
{
    public function test()
    {
        Carbon::setTestNow('2024-08-11');
        Carbon::setTestNow(Carbon::parse('2024-08-11'));
    }
}

?>

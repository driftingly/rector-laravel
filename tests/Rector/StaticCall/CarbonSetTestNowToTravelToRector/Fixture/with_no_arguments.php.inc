<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Carbon\Carbon;

class SomeTest extends TestCase
{
    public function test()
    {
        Carbon::setTestNow();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Carbon\Carbon;

class SomeTest extends TestCase
{
    public function test()
    {
        $this->travelTo(null);
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Carbon\CarbonImmutable;

class SomeTest extends TestCase
{
    public function test()
    {
        CarbonImmutable::setTestNow('2024-08-11');
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2024-08-11'));
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Carbon\CarbonImmutable;

class SomeTest extends TestCase
{
    public function test()
    {
        $this->travelTo('2024-08-11');
        $this->travelTo(CarbonImmutable::parse('2024-08-11'));
    }
}

?>

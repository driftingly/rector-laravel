<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Foo\Bar;

class SomeTest extends TestCase
{
    public function test()
    {
        Bar::setTestNow('2024-08-11');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonSetTestNowToTravelToRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;
use Foo\Bar;

class SomeTest extends TestCase
{
    public function test()
    {
        Bar::setTestNow('2024-08-11');
    }
}

?>

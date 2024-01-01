<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class ApplyWithStringsTest extends TestCase
{
    public function testSomething()
    {
        $this->expectsJobs(['\App\Jobs\SomeJob', '\App\Jobs\SomeOtherJob']);
        $this->expectsEvents('\App\Events\SomeEvent');

        $this->get('/');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class ApplyWithStringsTest extends TestCase
{
    public function testSomething()
    {
        \Illuminate\Support\Facades\Bus::fake(['\App\Jobs\SomeJob', '\App\Jobs\SomeOtherJob']);
        \Illuminate\Support\Facades\Event::fake(['\App\Events\SomeEvent']);

        $this->get('/');
        \Illuminate\Support\Facades\Bus::assertDispatched('\App\Jobs\SomeJob');
        \Illuminate\Support\Facades\Bus::assertDispatched('\App\Jobs\SomeOtherJob');
        \Illuminate\Support\Facades\Event::assertDispatched('\App\Events\SomeEvent');
    }
}
?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class BaseTest extends TestCase
{
    public function testSomething()
    {
        $this->expectsJobs([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);
        $this->expectsEvents(\App\Events\SomeEvent::class);

        $this->get('/');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class BaseTest extends TestCase
{
    public function testSomething()
    {
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);
        \Illuminate\Support\Facades\Event::fake([\App\Events\SomeEvent::class]);

        $this->get('/');
        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeJob::class);
        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeOtherJob::class);
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\SomeEvent::class);
    }
}
?>

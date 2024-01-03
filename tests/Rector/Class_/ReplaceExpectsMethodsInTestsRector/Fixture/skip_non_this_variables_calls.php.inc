<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class NotThisVarTest extends TestCase
{
    public function testSomething()
    {
        $variable->expectsJobs([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);
        $variable->expectsEvents(\App\Events\SomeEvent::class);
    }
}
?>

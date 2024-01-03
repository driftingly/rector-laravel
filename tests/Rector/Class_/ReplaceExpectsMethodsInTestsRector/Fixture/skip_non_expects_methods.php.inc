<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceExpectsMethodsInTestsRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class NonExpectsMethodTest extends TestCase
{
    public function testSomething()
    {
        $this->expectsSomething([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);

        $this->get('/');
    }
}
?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class SomeTest extends TestCase
{
    public function testSomething()
    {
        $this->withoutJobs();
        $this->withoutEvents();
        $this->withoutNotifications();

        $this->get('/');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector\Fixture;

use Illuminate\Foundation\Testing\TestCase;

class SomeTest extends TestCase
{
    public function testSomething()
    {
        \Illuminate\Support\Facades\Bus::fake();
        \Illuminate\Support\Facades\Event::fake();
        \Illuminate\Support\Facades\Notification::fake();

        $this->get('/');
    }
}

?>

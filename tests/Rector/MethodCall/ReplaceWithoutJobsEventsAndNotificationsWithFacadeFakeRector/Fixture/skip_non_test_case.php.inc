<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector\Fixture;


class SkipNonTestCaseTest extends TestCase
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

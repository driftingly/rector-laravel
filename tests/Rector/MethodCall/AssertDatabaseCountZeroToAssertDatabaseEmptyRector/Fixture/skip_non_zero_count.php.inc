<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class SkipNonZeroCountTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testFiveUsers()
    {
        $this->assertDatabaseCount('users', 5);
    }
}

?>

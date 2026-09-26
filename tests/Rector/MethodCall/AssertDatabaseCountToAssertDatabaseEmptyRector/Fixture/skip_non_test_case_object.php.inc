<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class SkipNonTestCaseObjectTest
{
    public function testNoUsers()
    {
        $this->assertDatabaseCount('users', 0);
    }

    public function testOtherObject($assertions)
    {
        $assertions->assertDatabaseCount('users', 0);
    }
}

?>

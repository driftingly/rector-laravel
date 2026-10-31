<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class SkipUnresolvableCountTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testVariableCount($count)
    {
        $this->assertDatabaseCount('users', $count);
    }

    public function testMethodCallCount()
    {
        $this->assertDatabaseCount('users', $this->expectedCount());
    }

    private function expectedCount(): int
    {
        return 0;
    }
}

?>

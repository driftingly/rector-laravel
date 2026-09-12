<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class SkipSpreadArgumentsTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testAllSpread(array $arguments)
    {
        $this->assertDatabaseCount(...$arguments);
    }

    public function testSpreadAfterTable(array $arguments)
    {
        $this->assertDatabaseCount('users', ...$arguments);
    }

    public function testSpreadInConnectionSlot(array $arguments)
    {
        $this->assertDatabaseCount('users', 0, ...$arguments);
    }
}

?>

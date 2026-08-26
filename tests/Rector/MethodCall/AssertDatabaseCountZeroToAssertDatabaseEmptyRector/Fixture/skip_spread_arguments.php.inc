<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

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
}

?>

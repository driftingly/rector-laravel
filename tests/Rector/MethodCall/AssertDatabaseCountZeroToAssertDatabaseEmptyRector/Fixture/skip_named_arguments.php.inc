<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class SkipNamedArgumentsTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testAllNamed()
    {
        $this->assertDatabaseCount(table: 'users', count: 0);
    }

    public function testPartiallyNamed()
    {
        $this->assertDatabaseCount('users', count: 0);
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class SkipWrongArgCountTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testMissingCount()
    {
        $this->assertDatabaseCount('users');
    }

    public function testTooManyArguments()
    {
        $this->assertDatabaseCount('users', 0, 'other', 'extra');
    }
}

?>

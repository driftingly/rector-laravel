<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class SkipFirstClassCallableTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $assertCount = $this->assertDatabaseCount(...);

        $assertCount('users', 0);
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class SkipFirstClassCallableTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $assertCount = $this->assertDatabaseCount(...);

        $assertCount('users', 0);
    }
}

?>

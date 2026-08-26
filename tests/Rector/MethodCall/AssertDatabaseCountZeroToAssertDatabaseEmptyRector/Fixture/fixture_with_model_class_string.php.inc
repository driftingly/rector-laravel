<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Source\User;

class FixtureWithModelClassStringTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseCount(User::class, 0);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Source\User;

class FixtureWithModelClassStringTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseEmpty(User::class);
    }
}

?>

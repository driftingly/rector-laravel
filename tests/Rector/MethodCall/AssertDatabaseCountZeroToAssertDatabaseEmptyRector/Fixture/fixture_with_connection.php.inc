<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class FixtureWithConnectionTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseCount('users', 0, 'other');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class FixtureWithConnectionTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseEmpty('users', 'other');
    }
}

?>

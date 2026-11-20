<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class FixtureWithModelClassTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseCount(User::class, 0);
    }

    public function testNoUsersOnConnection()
    {
        $this->assertDatabaseCount(User::class, 0, 'other');
    }

    public function testNamedModelClass()
    {
        $this->assertDatabaseCount(table: User::class, count: 0);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class FixtureWithModelClassTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseEmpty(User::class);
    }

    public function testNoUsersOnConnection()
    {
        $this->assertDatabaseEmpty(User::class, 'other');
    }

    public function testNamedModelClass()
    {
        $this->assertDatabaseEmpty(table: User::class);
    }
}

?>

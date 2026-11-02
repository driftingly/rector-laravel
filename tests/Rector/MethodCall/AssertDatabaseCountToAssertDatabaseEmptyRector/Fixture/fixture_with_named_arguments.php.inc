<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class FixtureWithNamedArgumentsTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testAllNamed()
    {
        $this->assertDatabaseCount(table: 'users', count: 0);
    }

    public function testPartiallyNamed()
    {
        $this->assertDatabaseCount('users', count: 0);
    }

    public function testNamedConnection()
    {
        $this->assertDatabaseCount('users', 0, connection: 'other');
    }

    public function testOutOfOrder()
    {
        $this->assertDatabaseCount(count: 0, table: 'users', connection: 'other');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class FixtureWithNamedArgumentsTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testAllNamed()
    {
        $this->assertDatabaseEmpty(table: 'users');
    }

    public function testPartiallyNamed()
    {
        $this->assertDatabaseEmpty('users');
    }

    public function testNamedConnection()
    {
        $this->assertDatabaseEmpty('users', connection: 'other');
    }

    public function testOutOfOrder()
    {
        $this->assertDatabaseEmpty(table: 'users', connection: 'other');
    }
}

?>

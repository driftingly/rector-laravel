<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class FixtureTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseCount('users', 0);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountToAssertDatabaseEmptyRector\Fixture;

class FixtureTest extends \Illuminate\Foundation\Testing\TestCase
{
    public function testNoUsers()
    {
        $this->assertDatabaseEmpty('users');
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class FixtureWithConstantCountTest extends \Illuminate\Foundation\Testing\TestCase
{
    private const EXPECTED_COUNT = 0;

    public function testNoUsers()
    {
        $this->assertDatabaseCount('users', self::EXPECTED_COUNT);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

class FixtureWithConstantCountTest extends \Illuminate\Foundation\Testing\TestCase
{
    private const EXPECTED_COUNT = 0;

    public function testNoUsers()
    {
        $this->assertDatabaseEmpty('users');
    }
}

?>

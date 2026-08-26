<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

class FixtureWithConcernOnlyTest extends \PHPUnit\Framework\TestCase
{
    use InteractsWithDatabase;

    public function testNoUsers()
    {
        $this->assertDatabaseCount('users', 0);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertDatabaseCountZeroToAssertDatabaseEmptyRector\Fixture;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

class FixtureWithConcernOnlyTest extends \PHPUnit\Framework\TestCase
{
    use InteractsWithDatabase;

    public function testNoUsers()
    {
        $this->assertDatabaseEmpty('users');
    }
}

?>

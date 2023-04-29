<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipWithNonAssertStatusMethodCallTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatuses();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipWithNonAssertStatusMethodCallTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatuses();
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipMissingStatusCodeTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipMissingStatusCodeTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus();
    }
}

?>

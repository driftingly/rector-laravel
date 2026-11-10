<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipMultiplePossibleStatusCodesTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response, bool $allowed)
    {
        $response->assertStatus($allowed ? 200 : 403);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipMultiplePossibleStatusCodesTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response, bool $allowed)
    {
        $response->assertStatus($allowed ? 200 : 403);
    }
}

?>

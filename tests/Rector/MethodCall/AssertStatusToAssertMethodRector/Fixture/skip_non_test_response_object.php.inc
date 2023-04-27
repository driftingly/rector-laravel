<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipNonTestResponseObjectTest
{
    public function testOk($response)
    {
        $response->assertStatus(200);
    }

    public function testNoContent($response)
    {
        $response->assertStatus(204);
    }

    public function testForbidden($response)
    {
        $response->assertStatus(403);
    }

    public function testNotFound($response)
    {
        $response->assertStatus(404);
    }

    public function testUnauthorized($response)
    {
        $response->assertStatus(401);
    }

    public function testUnprocessableEntity($response)
    {
        $response->assertStatus(422);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipNonTestResponseObjectTest
{
    public function testOk($response)
    {
        $response->assertStatus(200);
    }

    public function testNoContent($response)
    {
        $response->assertStatus(204);
    }

    public function testForbidden($response)
    {
        $response->assertStatus(403);
    }

    public function testNotFound($response)
    {
        $response->assertStatus(404);
    }

    public function testUnauthorized($response)
    {
        $response->assertStatus(401);
    }

    public function testUnprocessableEntity($response)
    {
        $response->assertStatus(422);
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(200);
    }

    public function testNoContent(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(204);
    }

    public function testForbidden(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(403);
    }

    public function testNotFound(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(404);
    }

    public function testMethodNotAllowed(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(405);
    }

    public function testUnauthorized(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(401);
    }

    public function testUnprocessableEntity(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(422);
    }

    public function testGone(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(410);
    }

    public function testInternalServerError(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(500);
    }

    public function testServiceUnavailable(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(503);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertOk();
    }

    public function testNoContent(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertNoContent();
    }

    public function testForbidden(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertForbidden();
    }

    public function testNotFound(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertNotFound();
    }

    public function testMethodNotAllowed(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertMethodNotAllowed();
    }

    public function testUnauthorized(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertUnauthorized();
    }

    public function testUnprocessableEntity(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertUnprocessable();
    }

    public function testGone(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertGone();
    }

    public function testInternalServerError(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertInternalServerError();
    }

    public function testServiceUnavailable(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertServiceUnavailable();
    }
}

?>

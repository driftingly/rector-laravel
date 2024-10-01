<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithIlluminateTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_OK);
    }

    public function testNoContent(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
    }

    public function testForbidden(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_FORBIDDEN);
    }

    public function testNotFound(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_NOT_FOUND);
    }

    public function testMethodNotAllowed(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testUnauthorized(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_UNAUTHORIZED);
    }

    public function testUnprocessableEntity(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGone(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_GONE);
    }

    public function testInternalServerError(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testServiceUnavailable(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_SERVICE_UNAVAILABLE);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithIlluminateTest
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

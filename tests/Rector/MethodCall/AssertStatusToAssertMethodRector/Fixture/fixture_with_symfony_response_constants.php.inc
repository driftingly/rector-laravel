<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithSymfonyResponseTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function testNoContent(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function testForbidden(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
    }

    public function testNotFound(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
    }

    public function testMethodNotAllowed(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testUnauthorized(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
    }

    public function testUnprocessableEntity(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithSymfonyResponseTest
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
}

?>

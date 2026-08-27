<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithIlluminateTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_OK);
    }

    public function testCreated(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_CREATED);
    }

    public function testAccepted(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_ACCEPTED);
    }

    public function testNoContent(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
    }

    public function testMovedPermanently(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_MOVED_PERMANENTLY);
    }

    public function testFound(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_FOUND);
    }

    public function testNotModified(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_NOT_MODIFIED);
    }

    public function testTemporaryRedirect(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function testPermanentRedirect(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_PERMANENTLY_REDIRECT);
    }

    public function testBadRequest(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_BAD_REQUEST);
    }

    public function testUnauthorized(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_UNAUTHORIZED);
    }

    public function testPaymentRequired(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_PAYMENT_REQUIRED);
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

    public function testNotAcceptable(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_NOT_ACCEPTABLE);
    }

    public function testRequestTimeout(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_REQUEST_TIMEOUT);
    }

    public function testConflict(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_CONFLICT);
    }

    public function testGone(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_GONE);
    }

    public function testUnsupportedMediaType(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    public function testUnprocessableEntity(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testFailedDependency(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_FAILED_DEPENDENCY);
    }

    public function testTooManyRequests(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(\Illuminate\Http\Response::HTTP_TOO_MANY_REQUESTS);
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

    public function testCreated(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertCreated();
    }

    public function testAccepted(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertAccepted();
    }

    public function testNoContent(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertNoContent();
    }

    public function testMovedPermanently(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertMovedPermanently();
    }

    public function testFound(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertFound();
    }

    public function testNotModified(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertNotModified();
    }

    public function testTemporaryRedirect(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertTemporaryRedirect();
    }

    public function testPermanentRedirect(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertPermanentRedirect();
    }

    public function testBadRequest(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertBadRequest();
    }

    public function testUnauthorized(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertUnauthorized();
    }

    public function testPaymentRequired(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertPaymentRequired();
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

    public function testNotAcceptable(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertNotAcceptable();
    }

    public function testRequestTimeout(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertRequestTimeout();
    }

    public function testConflict(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertConflict();
    }

    public function testGone(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertGone();
    }

    public function testUnsupportedMediaType(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertUnsupportedMediaType();
    }

    public function testUnprocessableEntity(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertUnprocessable();
    }

    public function testFailedDependency(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertFailedDependency();
    }

    public function testTooManyRequests(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertTooManyRequests();
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

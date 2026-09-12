<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureTest
{
    public function testOk(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(200);
    }

    public function testCreated(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(201);
    }

    public function testAccepted(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(202);
    }

    public function testNoContent(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(204);
    }

    public function testMovedPermanently(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(301);
    }

    public function testFound(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(302);
    }

    public function testNotModified(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(304);
    }

    public function testTemporaryRedirect(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(307);
    }

    public function testPermanentRedirect(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(308);
    }

    public function testBadRequest(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(400);
    }

    public function testUnauthorized(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(401);
    }

    public function testPaymentRequired(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(402);
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

    public function testNotAcceptable(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(406);
    }

    public function testRequestTimeout(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(408);
    }

    public function testConflict(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(409);
    }

    public function testGone(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(410);
    }

    public function testUnsupportedMediaType(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(415);
    }

    public function testUnprocessableEntity(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(422);
    }

    public function testFailedDependency(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(424);
    }

    public function testTooManyRequests(\Illuminate\Testing\TestResponse $response)
    {
        $response->assertStatus(429);
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

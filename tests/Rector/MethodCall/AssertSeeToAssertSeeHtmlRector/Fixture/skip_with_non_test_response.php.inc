<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithNonTestResponse
{
    public function testSeeHtml($response)
    {
        $response->assertSee("<li>foo</li>", false);
        $response->assertSee("<li>foo</li>", false);
        $response->assertDontSee("<li>bar</li>", false);
    }
}
?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Testing\TestResponse;

class FixtureWithHtmlEscapeTrueOrMissing
{
    public function testSeeHtml(TestResponse $response)
    {
        $response->assertSee("<li>foo</li>", true);
        $response->assertSee("<li>foo</li>", 1);
        $response->assertDontSee("<li>bar</li>");
    }
}
?>

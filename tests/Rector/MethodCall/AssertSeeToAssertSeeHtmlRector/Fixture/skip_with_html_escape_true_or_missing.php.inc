<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Testing\TestResponse;

class FixtureWithHtmlEscapeTrueOrMissing
{
    public function testSeeHtml(TestResponse $response)
    {
        $response->assertSee("<li>foo</li>", true);
        $response->assertSee("<li>foo</li>", 1);
        $response->assertSee("<li>bar</li>");
        $response->assertDontSeeHtml("<li>bar</li>", true);
        $response->assertDontSeeHtml("<li>bar</li>", 1);
        $response->assertDontSeeHtml("<li>bar</li>");
        $response->assertSeeInOrder(["<li>foo</li>", "<li>bar</li>", "<li>baz</li>"], true);
        $response->assertSeeInOrder(["<li>foo</li>", "<li>bar</li>", "<li>baz</li>"], 1);
        $response->assertSeeInOrder(["<li>foo</li>", "<li>bar</li>", "<li>baz</li>"]);
    }
}
?>

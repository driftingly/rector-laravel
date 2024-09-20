<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Testing\TestResponse;

class FixtureWithHtmlEscapeFalse
{
    public function testSeeHtml(TestResponse $response)
    {
        $response->assertSee("<li>foo</li>", false);
        $response->assertDontSee("<li>bar</li>", false);
        $response->assertSeeInOrder(["<li>foo</li>", "<li>bar</li>", "<li>baz</li>"], false);
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Testing\TestResponse;

class FixtureWithHtmlEscapeFalse
{
    public function testSeeHtml(TestResponse $response)
    {
        $response->assertSeeHtml("<li>foo</li>");
        $response->assertDontSeeHtml("<li>bar</li>");
        $response->assertSeeHtmlInOrder(["<li>foo</li>", "<li>bar</li>", "<li>baz</li>"]);
    }
}
?>

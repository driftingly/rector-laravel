<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Testing\TestResponse;

class FixtureWithHtmlEscapeTrueOrMissing
{
    public function testSeeHtml(TestResponse $response)
    {
        $response->assertSomethingElse("<li>foo</li>", false);
        $response->assertSeeText("<li>foo</li>", false);
    }
}
?>

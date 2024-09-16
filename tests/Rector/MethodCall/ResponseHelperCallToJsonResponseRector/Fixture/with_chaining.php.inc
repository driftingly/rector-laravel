<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector\Fixture;

class FixtureWithResponseHelperCalls
{
    public function testWithChaining()
    {
        return response()->json()->status();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector\Fixture;

class FixtureWithResponseHelperCalls
{
    public function testWithChaining()
    {
        return (new \Illuminate\Http\JsonResponse())->status();
    }
}

?>

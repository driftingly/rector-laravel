<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector\Fixture;

class FixtureWithResponseHelperCalls
{
    public function testNoArguments()
    {
        return response()->json();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector\Fixture;

class FixtureWithResponseHelperCalls
{
    public function testNoArguments()
    {
        return new \Illuminate\Http\JsonResponse();
    }
}

?>

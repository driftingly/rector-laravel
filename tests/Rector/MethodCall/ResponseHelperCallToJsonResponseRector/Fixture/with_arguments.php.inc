<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector\Fixture;

class FixtureWithResponseHelperCalls
{
    public function testWithArguments()
    {
        return response()->json(['key' => 'value']);
    }

    public function testWithVariableAsArgument()
    {
        $a = new \stdClass();
        return response()->json($a);
    }

    public function testWithAllArguments()
    {
        return response()->json(['key' => 'value'], 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT, true);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector\Fixture;

class FixtureWithResponseHelperCalls
{
    public function testWithArguments()
    {
        return new \Illuminate\Http\JsonResponse(['key' => 'value']);
    }

    public function testWithVariableAsArgument()
    {
        $a = new \stdClass();
        return new \Illuminate\Http\JsonResponse($a);
    }

    public function testWithAllArguments()
    {
        return new \Illuminate\Http\JsonResponse(['key' => 'value'], 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT, true);
    }
}

?>

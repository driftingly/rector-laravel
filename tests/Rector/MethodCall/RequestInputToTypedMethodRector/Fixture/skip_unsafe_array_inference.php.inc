<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipUnsafeArrayInference
{
    public function store(Request $request)
    {
        // A default does not change a present null or scalar into an array.
        $metrics = $request->input('metrics', []);
        $objectId = $request->input('object')['id'];
        $firstCharacter = $request->input('name')[0];
        $nonEmptyDefault = (array) $request->input('ids', [1]);
        $nullDefault = (array) $request->input('ids', null);
        // input() treats this as path segments, while array() treats it as selected keys.
        $path = (array) $request->input(['user', 'ids']);
    }
}

?>

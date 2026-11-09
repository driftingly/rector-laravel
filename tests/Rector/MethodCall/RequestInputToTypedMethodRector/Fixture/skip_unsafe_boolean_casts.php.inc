<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipUnsafeBooleanCasts
{
    public function store(Request $request)
    {
        // PHP casts "false", "off", "no", and 2 to true; boolean() returns false.
        $active = (bool) $request->input('active');
        $withDefault = (bool) $request->input('active', true);
        $falseDefault = (bool) $request->input('active', false);
        $coalesced = (bool) ($request->input('active') ?? null);
    }
}

?>

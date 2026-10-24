<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipUnsafeArguments
{
    public function store(Request $request, array $arguments)
    {
        $namedKey = (int) $request->input(key: 'age');
        $namedDefault = (string) $request->input('name', default: 'Guest');
        $reordered = (float) $request->input(default: 1.5, key: 'price');
        $unpacked = (int) $request->input(...$arguments);
        $extraArgument = (float) $request->input('price', 0, 'extra');
        $missingKey = (int) $request->input();
    }
}

?>

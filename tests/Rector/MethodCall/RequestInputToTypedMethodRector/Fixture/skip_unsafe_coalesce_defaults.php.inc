<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipUnsafeCoalesceDefaults
{
    public function store(Request $request)
    {
        $name = (string) ($request->input('name') ?? 'Guest');
        $age = (int) ($request->input('age') ?? 18);
        $price = (float) ($request->input('price') ?? 1.5);
        $ids = (array) ($request->input('ids') ?? [1]);
    }
}

?>

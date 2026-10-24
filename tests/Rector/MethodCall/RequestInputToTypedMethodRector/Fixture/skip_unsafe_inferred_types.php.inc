<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipUnsafeInferredTypes
{
    public function store(Request $request, ?string $name, int $age, float $price, bool $active, \Carbon\Carbon $date)
    {
        $name = $request->input('name');
        $age = $request->input('age');
        $price = $request->input('price');
        $active = $request->input('active');
        $date = $request->input('date');
        $name = '';
        $name = $request->input('name');
        $name = $request->input('name', '');
    }
}

?>

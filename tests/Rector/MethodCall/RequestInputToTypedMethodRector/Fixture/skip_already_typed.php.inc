<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipAlreadyTyped
{
    public function store(Request $request)
    {
        // Should not change if already using typed methods
        $name = $request->string('name');
        $age = $request->integer('age');
        $price = $request->float('price');
        $isActive = $request->boolean('is_active');
        $date = $request->date('date');

        // Should not change if no cast
        $data = $request->input('data');
        $value = $request->get('value');
    }
}

?>

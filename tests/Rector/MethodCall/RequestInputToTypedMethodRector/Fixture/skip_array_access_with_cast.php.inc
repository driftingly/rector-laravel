<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class ArrayAccessWithCast
{
    public function store(Request $request)
    {
        $name = (string) $request['name'];
        $age = (int) $request['age'];
        $price = (float) $request['price'];
        $isActive = (bool) $request['is_active'];
    }
}

?>

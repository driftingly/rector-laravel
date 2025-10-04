<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastToFloat
{
    public function store(Request $request)
    {
        $price = (float) $request->input('price');
        $amount = (double) $request->get('amount');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastToFloat
{
    public function store(Request $request)
    {
        $price = $request->float('price');
        $amount = $request->float('amount');
    }
}

?>

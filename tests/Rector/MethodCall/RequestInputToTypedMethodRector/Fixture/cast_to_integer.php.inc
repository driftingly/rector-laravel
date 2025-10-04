<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastToInteger
{
    public function store(Request $request)
    {
        $age = (int) $request->input('age');
        $count = (int) $request->get('count');
        $quantity = (int) $request->data('quantity');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastToInteger
{
    public function store(Request $request)
    {
        $age = $request->integer('age');
        $count = $request->integer('count');
        $quantity = $request->integer('quantity');
    }
}

?>

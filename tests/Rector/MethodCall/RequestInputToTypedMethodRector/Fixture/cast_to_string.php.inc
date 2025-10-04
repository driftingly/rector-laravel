<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastToString
{
    public function store(Request $request)
    {
        $name = (string) $request->input('name');
        $email = (string) $request->get('email');
        $address = (string) $request->data('address');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastToString
{
    public function store(Request $request)
    {
        $name = $request->string('name');
        $email = $request->string('email');
        $address = $request->string('address');
    }
}

?>

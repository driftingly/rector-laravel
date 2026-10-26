<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class InputPaths
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $age = (int) $request->input('user.age');
        $names = (array) $request->input('users.*.name');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class InputPaths
{
    private const PER_PAGE = 25;

    public function run(Request $request)
    {
        $age = $request->integer('user.age');
        $names = $request->array('users.*.name');
    }
}

?>

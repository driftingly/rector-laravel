<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

use Illuminate\Http\Request;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        $name = $request->get('name', 'default');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

use Illuminate\Http\Request;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        $name = $request->input('name', 'default');
    }
}
?>
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        $name = $request->get('name', 'default');
        $name = RequestFacade::get('name');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;

class SomeController
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        $name = $request->input('name', 'default');
        $name = RequestFacade::input('name');
    }
}
?>

<?php

namespace RectorLaravel\Tests\Sets\Laravel120;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;

function requestGetToInput(Request $request)
{
    $name = $request->get('name');
    $name = RequestFacade::get('name');
}

?>
----
<?php

namespace RectorLaravel\Tests\Sets\Laravel120;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;

function requestGetToInput(Request $request)
{
    $name = $request->input('name');
    $name = RequestFacade::input('name');
}

?>

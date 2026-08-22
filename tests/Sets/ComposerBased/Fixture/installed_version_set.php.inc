<?php

namespace RectorLaravel\Tests\Sets\ComposerBased\Fixture;

use Illuminate\Http\Request;

function requestGetToInput(Request $request)
{
    $name = $request->get('name');
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\ComposerBased\Fixture;

use Illuminate\Http\Request;

function requestGetToInput(Request $request)
{
    $name = $request->input('name');
}

?>

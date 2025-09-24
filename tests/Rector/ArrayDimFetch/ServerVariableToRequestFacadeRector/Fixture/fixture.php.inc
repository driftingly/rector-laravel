<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

$_SERVER['VARIABLE'];

$variable = $_SERVER['VARIABLE'];

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

\Illuminate\Support\Facades\Request::server('VARIABLE');

$variable = \Illuminate\Support\Facades\Request::server('VARIABLE');

?>

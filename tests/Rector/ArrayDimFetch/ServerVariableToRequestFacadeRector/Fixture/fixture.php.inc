<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

$_SERVER['VARIABLE'];

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

\Illuminate\Support\Facades\Request::server('VARIABLE');

?>

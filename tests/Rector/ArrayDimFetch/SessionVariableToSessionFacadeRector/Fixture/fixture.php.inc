<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\Fixture;

$_SESSION['VARIABLE'];
$_SESSION['VARIABLE'] = 'value';

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\Fixture;

\Illuminate\Support\Facades\Session::get('VARIABLE');
\Illuminate\Support\Facades\Session::put('VARIABLE', 'value');

?>

<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\Fixture;

$_SESSION['key'];
$_SESSION['key'] = 'value';
$_SESSION;
session_regenerate_id();
session_unset();
session_destroy();
session_start();
unset($_SESSION['key']);
isset($_SESSION['key']);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\Fixture;

\Illuminate\Support\Facades\Session::get('key');
\Illuminate\Support\Facades\Session::put('key', 'value');
\Illuminate\Support\Facades\Session::all();
\Illuminate\Support\Facades\Session::regenerate();
\Illuminate\Support\Facades\Session::flush();
\Illuminate\Support\Facades\Session::destroy();
\Illuminate\Support\Facades\Session::start();
\Illuminate\Support\Facades\Session::forget('key');
\Illuminate\Support\Facades\Session::has('key');

?>

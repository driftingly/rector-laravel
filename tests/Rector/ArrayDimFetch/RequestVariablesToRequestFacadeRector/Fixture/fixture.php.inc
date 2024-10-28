<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

$var = $_GET['a'];
$deepVar = $_GET['a']['b'];
$numberUsed = $_GET['a']['b'][0];
$postVar = $_POST['a'];

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

$var = \Illuminate\Support\Facades\Request::input('a');
$deepVar = \Illuminate\Support\Facades\Request::input('a.b');
$numberUsed = \Illuminate\Support\Facades\Request::input('a.b.0');
$postVar = \Illuminate\Support\Facades\Request::input('a');

?>

<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

$var = $_GET['a'];
$deepVar = $_GET['a']['b'];
$numberUsed = $_GET['a']['b'][0];
$postVar = $_POST['a'];
$requestVar = $_REQUEST['a'];
$query = $_GET;
$post = $_POST;
$input = $_REQUEST;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

$var = \Illuminate\Support\Facades\Request::query('a');
$deepVar = \Illuminate\Support\Facades\Request::query('a.b');
$numberUsed = \Illuminate\Support\Facades\Request::query('a.b.0');
$postVar = \Illuminate\Support\Facades\Request::post('a');
$requestVar = \Illuminate\Support\Facades\Request::input('a');
$query = \Illuminate\Support\Facades\Request::query();
$post = \Illuminate\Support\Facades\Request::post();
$input = \Illuminate\Support\Facades\Request::all();

?>

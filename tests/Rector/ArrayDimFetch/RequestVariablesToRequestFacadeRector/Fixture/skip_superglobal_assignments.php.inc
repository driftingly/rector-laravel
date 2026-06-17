<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

$_POST = $data;
$_GET = [];
$_REQUEST = $input;
$_POST['key'] = $value;
$_GET['key'] = 'value';
$_REQUEST['key'] = $value;

?>
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

if (isset($_GET['a'])) {

}

if (isset($_POST['b'])) {

}

if (isset($_REQUEST['c'])) {

}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

if (\Illuminate\Support\Facades\Request::query('a') !== null) {

}

if (\Illuminate\Support\Facades\Request::post('b') !== null) {

}

if (\Illuminate\Support\Facades\Request::exists('c')) {

}

?>

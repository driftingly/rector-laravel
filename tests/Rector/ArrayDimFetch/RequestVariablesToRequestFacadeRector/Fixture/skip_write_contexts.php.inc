<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector\Fixture;

function skipWriteContexts($value, array $list)
{
    $_GET['key'] .= 'suffix';
    $_POST['key'] ??= 'value';
    $_REQUEST['counter']++;
    --$_GET['counter'];
    $reference = &$_POST['key'];
    foreach ($list as $_GET['key']) {
    }
    [$_POST['key']] = ['value'];
    sort($_GET['list']);
    preg_match('/key/', 'key', $_POST['matches']);
    $_GET['nested']['key'] = $value;
}

?>

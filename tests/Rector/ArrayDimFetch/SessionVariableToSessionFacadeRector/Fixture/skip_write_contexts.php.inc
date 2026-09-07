<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\Fixture;

function skipWriteContexts($value, array $list)
{
    $_SESSION['key'] .= 'suffix';
    $_SESSION['key'] ??= 'value';
    $_SESSION['counter']++;
    --$_SESSION['counter'];
    $reference = &$_SESSION['key'];
    foreach ($list as $_SESSION['key']) {
    }
    [$_SESSION['key']] = ['value'];
    sort($_SESSION['list']);
    preg_match('/key/', 'key', $_SESSION['matches']);
    $_SESSION['nested']['key'] = $value;
}

?>

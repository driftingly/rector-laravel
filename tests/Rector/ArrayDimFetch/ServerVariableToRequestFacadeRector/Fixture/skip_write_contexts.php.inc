<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

function skipWriteContexts($value, array $list)
{
    $_SERVER['APP_ENV'] .= 'suffix';
    $_SERVER['APP_ENV'] ??= 'production';
    $_SERVER['COUNTER']++;
    --$_SERVER['COUNTER'];
    $reference = &$_SERVER['APP_ENV'];
    foreach ($list as $_SERVER['APP_ENV']) {
    }
    [$_SERVER['APP_ENV']] = ['production'];
    sort($_SERVER['LIST']);
    preg_match('/env/', 'env', $_SERVER['MATCHES']);
    $_SERVER['nested']['key'] = $value;
}

?>

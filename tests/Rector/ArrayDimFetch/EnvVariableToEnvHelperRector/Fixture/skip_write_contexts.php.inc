<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector\Fixture;

function skipWriteContexts(string $key, $value, array $list)
{
    $_ENV['APP_NAME'] .= 'suffix';
    $_ENV['APP_NAME'] ??= 'MyApp';
    $_ENV['COUNTER']++;
    --$_ENV['COUNTER'];
    $reference = &$_ENV['APP_NAME'];
    foreach ($list as $_ENV['APP_NAME']) {
    }
    [$_ENV['APP_NAME']] = ['MyApp'];
    sort($_ENV['LIST']);
    preg_match('/name/', 'name', $_ENV['MATCHES']);
    unset($_ENV[$key]);
    isset($_ENV['APP_NAME']);
    $_ENV['nested']['key'] = $value;
}

?>

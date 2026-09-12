<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector\Fixture;

echo "app {$_ENV['APP_NAME']}";
echo "app $_ENV[APP_NAME]";

?>

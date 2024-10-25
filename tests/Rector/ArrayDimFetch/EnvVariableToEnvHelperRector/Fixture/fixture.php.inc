<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector\Fixture;

$_ENV['APP_NAME'];

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector\Fixture;

\Illuminate\Support\Env::get('APP_NAME');

?>

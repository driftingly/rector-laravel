<?php

namespace RectorLaravel\Tests\Rector\Expr\AppEnvironmentComparisonToParameterRector\Fixture;

/** @var \Illuminate\Contracts\Foundation\Application $app */
$app->environment() === 'production';
'staging' == $app->environment();

if ($app->environment() === 'production') {
}

\Illuminate\Support\Facades\App::environment() === 'production';
\App::environment() === 'production';

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\AppEnvironmentComparisonToParameterRector\Fixture;

/** @var \Illuminate\Contracts\Foundation\Application $app */
$app->environment('production');
$app->environment('staging');

if ($app->environment('production')) {
}

\Illuminate\Support\Facades\App::environment('production');
\App::environment('production');

?>

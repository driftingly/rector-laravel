<?php

namespace RectorLaravel\Tests\Rector\Expr\AppEnvironmentComparisonToParameterRector\Fixture;

/** @var \Illuminate\Foundation\Application $app */
/** @var \Illuminate\Contracts\Foundation\Application $appContract */
$app->environment() === 'local';
$app->environment() === 'staging';
$app->environment() === 'production';
$appContract->environment() === 'local';
$appContract->environment() === 'staging';
$appContract->environment() === 'production';
'staging' == $app->environment();
'testing' != $app->environment();

if ($app->environment() !== 'production') {
}

\Illuminate\Support\Facades\App::environment() === 'local';
\Illuminate\Support\Facades\App::environment() === 'staging';
\Illuminate\Support\Facades\App::environment() === 'production';
\App::environment() === 'local';
\App::environment() === 'staging';
\App::environment() === 'production';

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\AppEnvironmentComparisonToParameterRector\Fixture;

/** @var \Illuminate\Foundation\Application $app */
/** @var \Illuminate\Contracts\Foundation\Application $appContract */
$app->isLocal();
$app->environment('staging');
$app->isProduction();
$appContract->environment('local');
$appContract->environment('staging');
$appContract->environment('production');
$app->environment('staging');
!$app->environment('testing');

if (!$app->isProduction()) {
}

\Illuminate\Support\Facades\App::isLocal();
\Illuminate\Support\Facades\App::environment('staging');
\Illuminate\Support\Facades\App::isProduction();
\App::isLocal();
\App::environment('staging');
\App::isProduction();

?>

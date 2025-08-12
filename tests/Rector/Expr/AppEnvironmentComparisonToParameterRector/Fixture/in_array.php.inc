<?php

namespace RectorLaravel\Tests\Rector\Expr\AppEnvironmentComparisonToParameterRector\Fixture;

/** @var \Illuminate\Foundation\Application $app */
/** @var \Illuminate\Contracts\Foundation\Application $appContract */
in_array($app->environment(), ['local']);
in_array($appContract->environment(), ['testing'], true);

if (! in_array($app->environment(), ['local', 'testing'], true)) {
}

in_array(\Illuminate\Support\Facades\App::environment(), ['local', 'testing']);
in_array(\App::environment(), ['local', 'testing']);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\AppEnvironmentComparisonToParameterRector\Fixture;

/** @var \Illuminate\Foundation\Application $app */
/** @var \Illuminate\Contracts\Foundation\Application $appContract */
$app->environment(['local']);
$appContract->environment(['testing']);

if (! $app->environment(['local', 'testing'])) {
}

\Illuminate\Support\Facades\App::environment(['local', 'testing']);
\App::environment(['local', 'testing']);

?>

<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config1('app.name') ?? false;

(new \Illuminate\Http\Request())->inputOf('value') ?? '';
(new \Illuminate\Http\RequestElse())->input('value') ?? '';

\Illuminate\Support\Env::getAgain('APP_NAME') ?? '';
\Illuminate\Support\EnvUtil::get('APP_NAME') ?? '';

?>

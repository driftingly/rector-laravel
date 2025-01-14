<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name') ?? throw new \Exception('No Google maps base URL configured.');

(new \Illuminate\Http\Request())->input('value') ?? throw new \Exception('No Google maps base URL configured.');

\Illuminate\Support\Env::get('APP_NAME') ?? throw new \Exception('No Google maps base URL configured.');

?>

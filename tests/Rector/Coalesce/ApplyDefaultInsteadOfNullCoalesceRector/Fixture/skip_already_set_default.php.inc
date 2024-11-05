<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name', false) ?? false;

(new \Illuminate\Http\Request())->input('value', '') ?? '';

\Illuminate\Support\Env::get('APP_NAME', '') ?? '';

?>

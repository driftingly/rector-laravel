<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name') ?? false;
config('app.name') ?? null;

(new \Illuminate\Http\Request())->input('value') ?? '';

\Illuminate\Support\Env::get('APP_NAME') ?? '';

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name', false);
config('app.name');

(new \Illuminate\Http\Request())->input('value', '');

\Illuminate\Support\Env::get('APP_NAME', '');

?>

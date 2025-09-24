<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name') ?? false;
config('app.name') ?? null;

\Illuminate\Support\Env::get('APP_NAME') ?? '';

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name', false);
config('app.name');

\Illuminate\Support\Env::get('APP_NAME', '');

?>

<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name') ?? false;
config('app.name') ?? null;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name', false);
config('app.name');

?>

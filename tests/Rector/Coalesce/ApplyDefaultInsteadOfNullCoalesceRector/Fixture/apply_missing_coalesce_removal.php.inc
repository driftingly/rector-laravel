<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name', 'blah') ?? false;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

config('app.name', 'blah');

?>

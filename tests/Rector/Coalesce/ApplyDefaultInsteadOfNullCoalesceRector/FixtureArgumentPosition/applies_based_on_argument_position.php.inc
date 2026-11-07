<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\FixtureArgumentPosition;

custom_get($target, 'key') ?? false;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\FixtureArgumentPosition;

custom_get($target, 'key', false);

?>

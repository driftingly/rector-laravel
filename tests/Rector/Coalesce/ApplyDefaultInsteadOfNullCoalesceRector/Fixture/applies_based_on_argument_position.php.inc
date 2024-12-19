<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

data_get([], 'key') ?? false;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

data_get([], 'key', false);

?>

<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Support\Collection;

new Collection(range(0, 100, 10));
collect(range(10, 0, 2));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Support\Collection;

Collection::range(0, 100, 10);
\Illuminate\Support\Collection::range(10, 0, 2);

?>

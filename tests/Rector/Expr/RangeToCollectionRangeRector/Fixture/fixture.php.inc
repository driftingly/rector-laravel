<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Support\Collection;

new Collection(range(1, 10));
new \Illuminate\Support\Collection(range($from, $to));
collect(range('a', 'z'));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Support\Collection;

Collection::range(1, 10);
\Illuminate\Support\Collection::range($from, $to);
\Illuminate\Support\Collection::range('a', 'z');

?>

<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Support\Collection;

collect(range('a', 'z'));
new Collection(range(0, 1, 0.25));
new Collection(range($unknownFrom, $unknownTo));

?>

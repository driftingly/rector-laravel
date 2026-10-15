<?php

namespace RectorLaravel\Tests\Rector\Expr\CollectionCountComparisonToIsEmptyRector\Fixture;

use ArrayObject;
use Illuminate\Support\Collection;

$collection = new Collection([1, 2, 3]);
$collection->count() > 1;
$collection->count() === 1;
$collection->count() >= 0;
$collection->count() > $threshold;
$collection->sum() > 0;
count($collection) > 0;
(new ArrayObject([1]))->count() > 0;

?>

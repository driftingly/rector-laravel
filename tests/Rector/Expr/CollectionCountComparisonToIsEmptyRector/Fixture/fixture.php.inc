<?php

namespace RectorLaravel\Tests\Rector\Expr\CollectionCountComparisonToIsEmptyRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([1, 2, 3]);

$collection->count() > 0;
$collection->count() >= 1;
$collection->count() !== 0;
$collection->count() != 0;
0 < $collection->count();
1 <= $collection->count();
0 !== $collection->count();

$collection->count() === 0;
$collection->count() == 0;
$collection->count() < 1;
$collection->count() <= 0;
0 === $collection->count();
0 >= $collection->count();
1 > $collection->count();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\CollectionCountComparisonToIsEmptyRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([1, 2, 3]);

$collection->isNotEmpty();
$collection->isNotEmpty();
$collection->isNotEmpty();
$collection->isNotEmpty();
$collection->isNotEmpty();
$collection->isNotEmpty();
$collection->isNotEmpty();

$collection->isEmpty();
$collection->isEmpty();
$collection->isEmpty();
$collection->isEmpty();
$collection->isEmpty();
$collection->isEmpty();
$collection->isEmpty();

?>

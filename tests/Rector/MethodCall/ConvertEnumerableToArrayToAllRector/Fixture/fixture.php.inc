<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ConvertEnumerableToArrayToAllRector\Fixture;

use Illuminate\Support\Collection;

/** @var Collection<int, int> $collection */
$collection->toArray();
/** @var Collection<int, int>|null $nullableCollection */
$nullableCollection->toArray();
$nullableCollection?->toArray();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ConvertEnumerableToArrayToAllRector\Fixture;

use Illuminate\Support\Collection;

/** @var Collection<int, int> $collection */
$collection->all();
/** @var Collection<int, int>|null $nullableCollection */
$nullableCollection->all();
$nullableCollection?->all();

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ConvertEnumerableToArrayToAllRector\Fixture;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/** @var Collection<int, Arrayable|int> $collection */
$collection->toArray();

?>

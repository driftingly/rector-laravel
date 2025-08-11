<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ConvertEnumerableToArrayToAllRector\Fixture;

use Illuminate\Support\Collection;

/** @var Collection<int, mixed> $collection */
$collection->toArray();

?>

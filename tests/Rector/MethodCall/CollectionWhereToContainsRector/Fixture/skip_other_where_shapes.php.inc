<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([['active' => true]]);
$collection->where('active')->isNotEmpty();
$collection->where(...$arguments)->isNotEmpty();
$collection->whereIn('active', [true])->isEmpty();
$collection->whereStrict('active')->isEmpty();
$collection->whereStrict(key: 'active', value: true)->isEmpty();
$collection->where('status', ...$rest)->isEmpty();
$collection->isEmpty();
$collection->where('active', true)->count();

?>

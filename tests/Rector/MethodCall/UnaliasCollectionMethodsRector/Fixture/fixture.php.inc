<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UnaliasCollectionMethodsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->average();
$collection->some(fn (?int $number): bool => is_null($number));
$collection->unlessEmpty(fn(Collection $collection) => $collection->push('Foo'));
$collection->unlessNotEmpty(fn(Collection $collection) => $collection->push('Foo'));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UnaliasCollectionMethodsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->avg();
$collection->contains(fn (?int $number): bool => is_null($number));
$collection->whenNotEmpty(fn(Collection $collection) => $collection->push('Foo'));
$collection->whenEmpty(fn(Collection $collection) => $collection->push('Foo'));

?>

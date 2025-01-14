<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UnaliasCollectionMethodsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->average();
$collection->some(fn (?int $number): bool => is_null($number));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UnaliasCollectionMethodsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->avg();
$collection->contains(fn (?int $number): bool => is_null($number));

?>

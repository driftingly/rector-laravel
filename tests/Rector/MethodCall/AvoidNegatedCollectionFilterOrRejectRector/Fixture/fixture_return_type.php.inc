<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector\Fixture;

use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number) => ! is_null($number));

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number) => ! is_null($number));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector\Fixture;

use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number): bool => is_null($number));

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number): bool => is_null($number));

?>

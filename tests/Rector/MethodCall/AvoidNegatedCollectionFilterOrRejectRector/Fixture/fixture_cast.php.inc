<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector\Fixture;

use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number): bool => ! $number);

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number): bool => ! $number);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector\Fixture;

use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number): bool => (bool) $number);

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number): bool => (bool) $number);

?>

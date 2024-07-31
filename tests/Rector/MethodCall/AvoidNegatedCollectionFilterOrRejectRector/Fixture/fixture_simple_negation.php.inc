<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector\Fixture;

use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number): bool => ! is_null($number))
    ->reject(fn (int $number): bool => ! ($number > 0));

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number): bool => ! ($number > 0));

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number): bool => ! ($number > 0));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector\Fixture;

use Illuminate\Support\Collection;

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number): bool => is_null($number))
    ->filter(fn (int $number): bool => $number > 0);

(new Collection([0, 1, null, -1]))
    ->reject(fn (?int $number): bool => $number > 0);

(new Collection([0, 1, null, -1]))
    ->filter(fn (?int $number): bool => $number > 0);

?>

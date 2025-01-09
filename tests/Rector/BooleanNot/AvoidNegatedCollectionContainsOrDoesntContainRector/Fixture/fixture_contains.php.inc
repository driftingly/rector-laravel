<?php

namespace RectorLaravel\Tests\Rector\BooleanNot\AvoidNegatedCollectionContainsOrDoesntContainRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->contains(fn (?int $number): bool => is_null($number));
! $collection->contains(fn (?int $number): bool => is_null($number));
! $collection->contains(function (?int $number): bool {
    return is_null($number);
});
! $collection->contains(1);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\BooleanNot\AvoidNegatedCollectionContainsOrDoesntContainRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([0, 1, null, -1]);
$collection->contains(fn (?int $number): bool => is_null($number));
$collection->doesntContain(fn (?int $number): bool => is_null($number));
$collection->doesntContain(function (?int $number): bool {
    return is_null($number);
});
$collection->doesntContain(1);

?>

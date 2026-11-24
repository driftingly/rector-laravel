<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Support\Collection;

function fixture(int $from, int $to): void
{
    new Collection(range(1, 10));
    new \Illuminate\Support\Collection(range($from, $to));
    collect(range(0, $to - 1));
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Support\Collection;

function fixture(int $from, int $to): void
{
    Collection::range(1, 10);
    \Illuminate\Support\Collection::range($from, $to);
    \Illuminate\Support\Collection::range(0, $to - 1);
}

?>

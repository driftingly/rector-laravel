<?php

namespace RectorLaravel\Tests\Rector\Expr\CollectionCountComparisonToIsEmptyRector\Fixture;

use Illuminate\Support\LazyCollection;

function hasRows(LazyCollection $rows): bool
{
    if ($rows->count() === 0) {
        return false;
    }

    return $rows->count() > 0;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\CollectionCountComparisonToIsEmptyRector\Fixture;

use Illuminate\Support\LazyCollection;

function hasRows(LazyCollection $rows): bool
{
    if ($rows->isEmpty()) {
        return false;
    }

    return $rows->isNotEmpty();
}

?>

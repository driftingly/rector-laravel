<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Database\Eloquent\Collection;

function hasOpenItems(Collection $items): bool
{
    if ($items->where('status', 'closed')->isEmpty()) {
        return true;
    }

    return $items->where('status', 'open')->isNotEmpty();
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Database\Eloquent\Collection;

function hasOpenItems(Collection $items): bool
{
    if ($items->doesntContain('status', 'closed')) {
        return true;
    }

    return $items->contains('status', 'open');
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use Illuminate\Support\Collection;

function hasKey(Collection $collection): bool
{
    return $collection->has('variations');
}

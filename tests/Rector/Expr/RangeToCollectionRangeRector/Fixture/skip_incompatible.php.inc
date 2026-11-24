<?php

namespace RectorLaravel\Tests\Rector\Expr\RangeToCollectionRangeRector\Fixture;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

new EloquentCollection(range(1, 10));
new LazyCollection(range(1, 10));
new Collection(array_reverse(range(1, 10)));
new Collection(range(...$bounds));
new Collection(range(start: 1, end: 10));
collect(value: range(1, 10));
collect(range(...));
collect(range(1, 10, 1, 99));
collect(range(1));
new $class(range(1, 10));
collect([range(1, 10)]);
collect();

?>

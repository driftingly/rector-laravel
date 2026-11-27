<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooBuilder;
use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooModel;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where(function (?FooBuilder $query) {
    $query->where('id', 1);
});

FooModel::where(fn (?FooBuilder $query) => $query->where('id', 1));

?>

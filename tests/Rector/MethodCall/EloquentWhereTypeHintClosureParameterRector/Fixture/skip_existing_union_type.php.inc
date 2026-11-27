<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\BarBuilder;
use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooBuilder;
use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooModel;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->orWhere(function (FooBuilder|BarBuilder $query) {
    $query->where('id', 1);
});

FooModel::where(fn (FooBuilder|BarBuilder $query) => $query->where('id', 1));

?>

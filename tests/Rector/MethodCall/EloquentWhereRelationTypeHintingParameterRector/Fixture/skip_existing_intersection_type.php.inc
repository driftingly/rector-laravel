<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\BarInterface;
use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\FooBuilder;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->whereDoesntHave('posts', function (FooBuilder&BarInterface $query) {
    $query->where('is_published', true);
});

$query->whereDoesntHaveMorph('posts', [], fn (FooBuilder&BarInterface $query) => $query->where('is_published', true));

?>

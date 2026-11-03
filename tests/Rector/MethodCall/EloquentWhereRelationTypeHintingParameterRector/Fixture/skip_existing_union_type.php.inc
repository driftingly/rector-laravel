<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\BarBuilder;
use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\FooBuilder;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->whereHas('posts', function (FooBuilder|BarBuilder $query) {
    $query->where('is_published', true);
});

$query->whereHasMorph('posts', [], fn (FooBuilder|BarBuilder $query) => $query->where('is_published', true));

?>

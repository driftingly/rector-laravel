<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\FooBuilder;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->orWhereHas('posts', function (?FooBuilder $query) {
    $query->where('is_published', true);
});

$query->orWhereHasMorph('posts', [], fn (?FooBuilder $query) => $query->where('is_published', true));

?>

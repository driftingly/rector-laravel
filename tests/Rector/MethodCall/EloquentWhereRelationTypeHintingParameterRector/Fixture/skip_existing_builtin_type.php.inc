<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->orWhereDoesntHave('posts', function (object $query) {
    $query->where('is_published', true);
});

$query->orWhereDoesntHaveMorph('posts', [], fn (mixed $query) => $query->where('is_published', true));

?>

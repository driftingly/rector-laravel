<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\FooModel;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->whereHas('posts');
$query->whereHas('posts', null);
$query->whereHasMorph('posts', '', null);

FooModel::whereHas('posts');
FooModel::whereHas('posts', null);
FooModel::whereHasMorph('posts', '');
FooModel::whereHasMorph('posts', '', null);

?>

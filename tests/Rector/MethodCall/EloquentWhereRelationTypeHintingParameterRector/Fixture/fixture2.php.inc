<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->whereHas('posts', fn ($query) =>
    $query->where('is_published', true)
);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->whereHas('posts', fn (\Illuminate\Contracts\Database\Query\Builder $query) =>
    $query->where('is_published', true)
);

?>

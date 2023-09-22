<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where(function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where(function (\Illuminate\Contracts\Database\Query\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>

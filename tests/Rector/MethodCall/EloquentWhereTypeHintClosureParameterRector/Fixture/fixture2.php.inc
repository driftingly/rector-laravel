<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooModel;

FooModel::where(function ($query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooModel;

FooModel::where(function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
    $query->where('id', 1)
        ->orWhere('id', 2);
});

?>

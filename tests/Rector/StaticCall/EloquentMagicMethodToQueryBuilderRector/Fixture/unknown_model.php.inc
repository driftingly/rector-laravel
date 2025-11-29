<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use Illuminate\Database\Eloquent\Model;

function (string $class, Model $model) {
    /** @var class-string<Model> $class */
    $class::max('sort') + 1;
    $model::max('sort') + 1;
};
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use Illuminate\Database\Eloquent\Model;

function (string $class, Model $model) {
    /** @var class-string<Model> $class */
    $class::query()->max('sort') + 1;
    $model::query()->max('sort') + 1;
};
?>

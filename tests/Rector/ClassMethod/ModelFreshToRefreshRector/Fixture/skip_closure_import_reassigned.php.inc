<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

$model = SomeModel::create();

$reload = function () use ($model): string {
    $model = SomeModel::create();

    return $model->fresh()->status;
};

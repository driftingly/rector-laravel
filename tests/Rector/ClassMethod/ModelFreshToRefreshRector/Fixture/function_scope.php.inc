<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

function reloadedStatus(): string
{
    $model = SomeModel::create();

    return $model->fresh()->status;
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

function reloadedStatus(): string
{
    $model = SomeModel::create();

    return $model->refresh()->status;
}

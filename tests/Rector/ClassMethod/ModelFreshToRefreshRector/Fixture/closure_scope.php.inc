<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

$reload = static function (): string {
    $model = SomeModel::create();

    return $model->fresh()->status;
};
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

$reload = static function (): string {
    $model = SomeModel::create();

    return $model->refresh()->status;
};

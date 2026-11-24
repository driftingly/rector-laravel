<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipParameterReassignedWithNewInstanceTest extends TestCase
{
    private function reload(SomeModel $model): string
    {
        $model = SomeModel::create();

        return $model->fresh()->status;
    }
}

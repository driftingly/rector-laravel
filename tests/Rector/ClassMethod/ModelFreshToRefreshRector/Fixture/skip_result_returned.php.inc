<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultReturnedTest extends TestCase
{
    public function reloadedModel(): ?SomeModel
    {
        $model = SomeModel::create();

        return $model->fresh();
    }

}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultInstanceofTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $this->assertTrue($model->fresh() instanceof SomeModel);
    }

}

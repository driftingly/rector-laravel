<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultCastToBoolTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $this->assertTrue((bool) $model->fresh());
    }

}

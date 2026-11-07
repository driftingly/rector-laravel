<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverAlsoPassedAsSnapshotTest extends TestCase
{
    public function testItComparesTheModelBeforeAndAfterTheReload(): void
    {
        $model = SomeModel::create();

        $this->assertSame($model, $model->fresh());
    }
}

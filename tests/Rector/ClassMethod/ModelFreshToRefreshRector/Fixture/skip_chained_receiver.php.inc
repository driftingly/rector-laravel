<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipChainedReceiverTest extends TestCase
{
    public function testItLeavesAChainedReceiverAlone(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->parent->fresh()->status);
    }
}

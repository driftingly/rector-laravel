<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipArrayAccessReceiverTest extends TestCase
{
    public function testItLeavesAnArrayAccessReceiverAlone(): void
    {
        $models = [SomeModel::create()];

        $this->assertSame('done', $models[0]->fresh()->status);
    }
}

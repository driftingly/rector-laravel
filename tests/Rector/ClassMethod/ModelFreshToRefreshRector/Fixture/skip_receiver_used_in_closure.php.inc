<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverUsedInClosureTest extends TestCase
{
    public function testItLeavesAClosureCapturedReceiverAlone(): void
    {
        $model = SomeModel::create();

        $readStatusLater = static fn(): string => $model->status;

        $this->assertSame('done', $model->fresh()->status);

        $this->assertSame('pending', $readStatusLater());
    }
}

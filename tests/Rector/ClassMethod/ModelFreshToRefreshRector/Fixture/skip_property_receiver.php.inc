<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipPropertyReceiverTest extends TestCase
{
    private SomeModel $model;

    public function testItLeavesAPropertyReceiverAlone(): void
    {
        $this->assertSame('done', $this->model->fresh()->status);
    }
}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverReadAfterStatementTest extends TestCase
{
    public function testItKeepsTheOriginalInstanceIntact(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh()->status);
        $this->assertSame('pending', $model->status);
    }
}

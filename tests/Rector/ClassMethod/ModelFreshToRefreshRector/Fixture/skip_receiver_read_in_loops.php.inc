<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverReadInLoopsTest extends TestCase
{
    public function testReceiverReadInWhileLoop(): void
    {
        $model = SomeModel::create();
        $attempts = 0;

        while ($attempts++ < 2) {
            $this->assertSame('pending', $model->status);
            $this->assertSame('done', $model->fresh()->status);
        }
    }

    public function testReceiverReadInForLoop(): void
    {
        $model = SomeModel::create();

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->assertSame('pending', $model->status);
            $this->assertSame('done', $model->fresh()->status);
        }
    }

    public function testReceiverReadInDoWhileLoop(): void
    {
        $model = SomeModel::create();
        $attempts = 0;

        do {
            $this->assertSame('pending', $model->status);
            $this->assertSame('done', $model->fresh()->status);
        } while ($attempts++ < 2);
    }

    public function testItLeavesALoopedReceiverAlone(): void
    {
        $model = SomeModel::create();

        foreach ([1, 2] as $ignored) {
            $this->assertSame('pending', $model->status);
            $this->assertSame('done', $model->fresh()->status);
        }
    }
}

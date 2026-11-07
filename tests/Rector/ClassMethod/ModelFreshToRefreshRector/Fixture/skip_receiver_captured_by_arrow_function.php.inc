<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverCapturedByArrowFunctionTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $readStatus = fn (): string => $model->status;

        $this->assertSame('done', $model->fresh()->status);
        $this->assertSame('pending', $readStatus());
    }

}

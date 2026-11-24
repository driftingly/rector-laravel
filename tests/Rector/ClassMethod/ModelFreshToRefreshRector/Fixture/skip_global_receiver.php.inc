<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipGlobalReceiverTest extends TestCase
{
    public function testIt(): void
    {
        global $model;

        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh()->status);
    }
}

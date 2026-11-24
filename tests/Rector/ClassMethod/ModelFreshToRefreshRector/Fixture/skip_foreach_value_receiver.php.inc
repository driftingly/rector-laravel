<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipForeachValueReceiverTest extends TestCase
{
    public function testIt(): void
    {
        foreach ([SomeModel::create()] as $model) {
            $this->assertSame('done', $model->fresh()->status);
        }
    }

}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipMethodParameterReceiverTest extends TestCase
{
    public function assertReloaded(SomeModel $model): void
    {
        $this->assertSame('done', $model->fresh()->status);
    }

}

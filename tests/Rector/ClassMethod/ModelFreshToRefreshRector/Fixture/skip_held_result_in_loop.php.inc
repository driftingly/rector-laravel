<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipHeldResultInLoopTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $snapshots = [];

        foreach ([1, 2] as $ignored) {
            $snapshots[] = $model->fresh();
        }

        $this->assertCount(2, $snapshots);
    }
}

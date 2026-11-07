<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipHeldResultInArrowFunctionTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $snapshots = array_map(fn (int $ignored): ?SomeModel => $model->fresh(), [1, 2]);

        $this->assertCount(2, $snapshots);
    }
}

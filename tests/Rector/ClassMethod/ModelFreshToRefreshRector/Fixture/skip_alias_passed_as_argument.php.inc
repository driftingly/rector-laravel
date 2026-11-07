<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipAliasPassedAsArgumentTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $this->keep($model->fill([]));

        $this->assertSame('done', $model->fresh()->status);
    }

    private function keep(SomeModel $model): void
    {
    }
}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipUnsavedModelFromFirstOrNewTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = SomeModel::firstOrNew(['id' => 1]);

        $this->assertSame('done', $model->fresh()->status);
    }
}

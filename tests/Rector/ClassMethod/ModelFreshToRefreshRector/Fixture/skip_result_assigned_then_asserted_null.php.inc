<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultAssignedThenAssertedNullTest extends TestCase
{
    public function testItAssertsTheAssignedRowIsGone(): void
    {
        $model = SomeModel::create();

        $freshModel = $model->fresh();

        $this->assertNull($freshModel);
    }
}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipFreshWithArgumentTest extends TestCase
{
    public function testItLeavesAnEagerLoadingArgumentAlone(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh('parent')->status);
    }
}

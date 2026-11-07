<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipNullsafeDereferenceTest extends TestCase
{
    public function testItLeavesANullsafeDereferenceAlone(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh()?->status);
    }
}

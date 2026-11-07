<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultComparedToNullInEveryFormTest extends TestCase
{
    public function testResultNotEqualNull(): void
    {
        $model = SomeModel::create();

        $this->assertTrue($model->fresh() != null);
    }

    public function testResultLooselyComparedToNull(): void
    {
        $model = SomeModel::create();
        $isGone = $model->fresh() == null;

        $this->assertTrue($isGone);
    }

    public function testNullLiteralOnTheLeft(): void
    {
        $model = SomeModel::create();
        $isGone = null === $model->fresh();

        $this->assertTrue($isGone);
    }

    public function testItComparesTheResultToNull(): void
    {
        $model = SomeModel::create();

        $this->assertSame(true, $model->fresh() === null);
    }
}

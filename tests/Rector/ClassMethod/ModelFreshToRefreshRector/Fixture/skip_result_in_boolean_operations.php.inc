<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultInBooleanOperationsTest extends TestCase
{
    public function testResultInBooleanOr(): void
    {
        $model = SomeModel::create();
        $model->fresh() || $this->addToAssertionCount(1);
    }

    public function testResultInLogicalAnd(): void
    {
        $model = SomeModel::create();
        $model->fresh() and $this->addToAssertionCount(1);
    }

    public function testResultInLogicalOr(): void
    {
        $model = SomeModel::create();
        $model->fresh() or $this->addToAssertionCount(1);
    }

    public function testResultInLogicalXor(): void
    {
        $model = SomeModel::create();
        $model->fresh() xor $this->addToAssertionCount(1);
    }

    public function testResultInBooleanOperation(): void
    {
        $model = SomeModel::create();

        $this->assertTrue($model->fresh() && true);
    }
}

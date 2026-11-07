<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipNullSensitiveAssertionsTest extends TestCase
{
    public function testAssertTrue(): void
    {
        $model = SomeModel::create();
        $this->assertTrue($model->fresh());
    }

    public function testAssertFalse(): void
    {
        $model = SomeModel::create();
        $this->assertFalse($model->fresh());
    }

    public function testAssertEmpty(): void
    {
        $model = SomeModel::create();
        $this->assertEmpty($model->fresh());
    }

    public function testAssertNotEmpty(): void
    {
        $model = SomeModel::create();

        $this->assertNotEmpty($model->fresh());
    }

    public function testAssertNotNull(): void
    {
        $model = SomeModel::create();
        $this->assertNotNull($model->fresh());
    }

    public function testIsNullFunction(): void
    {
        $model = SomeModel::create();
        $isGone = is_null($model->fresh());

        $this->assertTrue($isGone);
    }

    public function testStaticAssertNull(): void
    {
        $model = SomeModel::create();
        self::assertNull($model->fresh());
    }

    public function testFunctionAssertSameNull(): void
    {
        $model = SomeModel::create();
        \PHPUnit\Framework\assertSame(null, $model->fresh());
    }

    public function testAssertSameNullLiteral(): void
    {
        $model = SomeModel::create();

        $this->assertSame(null, $model->fresh());
    }

    public function testItAssertsTheRowIsGone(): void
    {
        $model = SomeModel::create();

        $this->assertNull($model->fresh());
    }
}

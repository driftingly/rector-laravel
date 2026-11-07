<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class StaticAndFunctionCallsTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        self::assertInstanceOf(SomeModel::class, $model->fresh());
        \PHPUnit\Framework\assertInstanceOf(SomeModel::class, $model->fresh());
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class StaticAndFunctionCallsTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        self::assertInstanceOf(SomeModel::class, $model->fresh());
        \PHPUnit\Framework\assertInstanceOf(SomeModel::class, $model->refresh());
    }
}

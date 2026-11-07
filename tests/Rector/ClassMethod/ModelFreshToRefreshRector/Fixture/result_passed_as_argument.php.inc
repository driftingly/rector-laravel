<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ResultPassedAsArgumentTest extends TestCase
{
    private SomeModel $model;

    public function testItPassesTheReloadedModelAsArgument(): void
    {
        $model = SomeModel::create();

        $this->assertSame($this->model, $model->fresh());
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ResultPassedAsArgumentTest extends TestCase
{
    private SomeModel $model;

    public function testItPassesTheReloadedModelAsArgument(): void
    {
        $model = SomeModel::create();

        $this->assertSame($this->model, $model->refresh());
    }
}

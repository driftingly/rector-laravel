<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class AssignedResultTest extends TestCase
{
    public function testItAssignsTheReloadedModel(): void
    {
        $model = SomeModel::create();

        $freshModel = $model->fresh();

        $this->assertSame('done', $freshModel->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class AssignedResultTest extends TestCase
{
    public function testItAssignsTheReloadedModel(): void
    {
        $model = SomeModel::create();

        $freshModel = $model->refresh();

        $this->assertSame('done', $freshModel->status);
    }
}

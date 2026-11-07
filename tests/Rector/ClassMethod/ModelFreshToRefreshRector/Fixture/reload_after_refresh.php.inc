<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReloadAfterRefreshTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $model->refresh();

        $this->assertSame('done', $model->fresh()->status);

        $model->refresh();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReloadAfterRefreshTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $model->refresh();

        $this->assertSame('done', $model->refresh()->status);

        $model->refresh();
    }
}

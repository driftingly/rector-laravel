<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReceiverAssignedNewInstanceTwiceTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReceiverAssignedNewInstanceTwiceTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $model = SomeModel::create();

        $this->assertSame('done', $model->refresh()->status);
    }
}

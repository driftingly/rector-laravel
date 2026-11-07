<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReceiverDereferencedBeforeTest extends TestCase
{
    public function testItAcceptsAnEarlierPropertyRead(): void
    {
        $model = SomeModel::create();

        $this->assertSame('pending', $model->status);

        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReceiverDereferencedBeforeTest extends TestCase
{
    public function testItAcceptsAnEarlierPropertyRead(): void
    {
        $model = SomeModel::create();

        $this->assertSame('pending', $model->status);

        $this->assertSame('done', $model->refresh()->status);
    }
}

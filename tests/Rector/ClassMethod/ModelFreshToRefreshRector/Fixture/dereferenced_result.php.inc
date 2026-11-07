<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class DereferencedResultTest extends TestCase
{
    public function testItReadsAReloadedProperty(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class DereferencedResultTest extends TestCase
{
    public function testItReadsAReloadedProperty(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->refresh()->status);
    }
}

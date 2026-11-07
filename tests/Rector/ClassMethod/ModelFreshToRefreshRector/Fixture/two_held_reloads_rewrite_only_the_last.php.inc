<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class TwoHeldReloadsRewriteOnlyTheLastTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $first = $model->fresh();
        $second = $model->fresh();

        $this->assertNotSame($first->status, $second->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class TwoHeldReloadsRewriteOnlyTheLastTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $first = $model->fresh();
        $second = $model->refresh();

        $this->assertNotSame($first->status, $second->status);
    }
}

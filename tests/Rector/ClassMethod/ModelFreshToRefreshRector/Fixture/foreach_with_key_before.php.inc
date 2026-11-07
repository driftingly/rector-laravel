<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ForeachWithKeyBeforeTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        foreach (['draft' => 1] as $status => $count) {
            $this->assertSame(1, $count);
        }

        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ForeachWithKeyBeforeTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        foreach (['draft' => 1] as $status => $count) {
            $this->assertSame(1, $count);
        }

        $this->assertSame('done', $model->refresh()->status);
    }
}

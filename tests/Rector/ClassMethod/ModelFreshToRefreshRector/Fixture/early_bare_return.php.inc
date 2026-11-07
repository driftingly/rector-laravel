<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class EarlyBareReturnTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        if ($model->status === 'done') {
            return;
        }

        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class EarlyBareReturnTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        if ($model->status === 'done') {
            return;
        }

        $this->assertSame('done', $model->refresh()->status);
    }
}

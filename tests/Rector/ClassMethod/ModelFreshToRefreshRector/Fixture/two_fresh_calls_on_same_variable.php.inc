<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class TwoFreshCallsOnSameVariableTest extends TestCase
{
    public function testItConvertsBothCalls(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh()->status);
        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class TwoFreshCallsOnSameVariableTest extends TestCase
{
    public function testItConvertsBothCalls(): void
    {
        $model = SomeModel::create();

        $this->assertSame('done', $model->refresh()->status);
        $this->assertSame('done', $model->refresh()->status);
    }
}

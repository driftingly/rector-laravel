<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class FirstClassCallableCallTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $assert = $this->assertSame(...);

        $assert('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class FirstClassCallableCallTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $assert = $this->assertSame(...);

        $assert('done', $model->refresh()->status);
    }
}

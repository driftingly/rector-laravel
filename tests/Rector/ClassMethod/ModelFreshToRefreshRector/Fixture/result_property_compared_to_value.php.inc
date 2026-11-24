<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ResultPropertyComparedToValueTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $other = SomeModel::create();

        $this->assertTrue($model->fresh() == $other);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ResultPropertyComparedToValueTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $other = SomeModel::create();

        $this->assertTrue($model->refresh() == $other);
    }
}

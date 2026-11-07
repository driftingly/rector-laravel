<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ModelFoundByStaticCallTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = SomeModel::find(1);

        $this->assertSame('done', $model->fresh()->status);
    }

}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ModelFoundByStaticCallTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = SomeModel::find(1);

        $this->assertSame('done', $model->refresh()->status);
    }

}

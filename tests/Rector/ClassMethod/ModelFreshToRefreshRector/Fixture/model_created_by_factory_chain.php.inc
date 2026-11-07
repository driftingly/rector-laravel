<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ModelCreatedByFactoryChainTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = SomeModel::factory()->create();

        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ModelCreatedByFactoryChainTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = SomeModel::factory()->create();

        $this->assertSame('done', $model->refresh()->status);
    }
}

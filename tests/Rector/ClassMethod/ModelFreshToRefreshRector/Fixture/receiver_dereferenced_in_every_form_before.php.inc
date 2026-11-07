<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReceiverDereferencedInEveryFormBeforeTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $this->assertSame('pending', $model?->status);
        $model->touch();
        $this->assertSame('pending', $model->load('items')->status);
        $this->assertSame('pending', $model['status']);
        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ReceiverDereferencedInEveryFormBeforeTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $this->assertSame('pending', $model?->status);
        $model->touch();
        $this->assertSame('pending', $model->load('items')->status);
        $this->assertSame('pending', $model['status']);
        $this->assertSame('done', $model->refresh()->status);
    }
}

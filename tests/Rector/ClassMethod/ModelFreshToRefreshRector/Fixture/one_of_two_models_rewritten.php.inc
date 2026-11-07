<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class OneOfTwoModelsRewrittenTest extends TestCase
{
    public function testIt(): void
    {
        $done = SomeModel::create();
        $gone = SomeModel::create();

        $this->assertSame('done', $done->fresh()->status);
        $this->assertNull($gone->fresh());
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class OneOfTwoModelsRewrittenTest extends TestCase
{
    public function testIt(): void
    {
        $done = SomeModel::create();
        $gone = SomeModel::create();

        $this->assertSame('done', $done->refresh()->status);
        $this->assertNull($gone->fresh());
    }
}

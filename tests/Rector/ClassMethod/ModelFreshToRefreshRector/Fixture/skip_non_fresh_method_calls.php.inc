<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipNonFreshMethodCallsTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $model->touch();

        $this->assertSame('id', $model->getKeyName());
    }
}

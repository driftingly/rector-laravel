<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipDynamicMethodNameCalledFreshTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $fresh = 'getKeyName';

        $this->assertSame('id', $model->$fresh());
    }
}

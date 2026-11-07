<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipByReferenceReceiverTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $alias = &$model;

        $this->assertSame('done', $model->fresh()->status);
    }

}

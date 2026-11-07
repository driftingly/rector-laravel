<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverAssignedFromVariableTest extends TestCase
{
    public function testIt(): void
    {
        $other = SomeModel::create();
        $model = $other;

        $this->assertSame('done', $model->fresh()->status);
    }

}

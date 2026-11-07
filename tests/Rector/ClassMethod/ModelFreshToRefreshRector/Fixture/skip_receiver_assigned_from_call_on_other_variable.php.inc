<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverAssignedFromCallOnOtherVariableTest extends TestCase
{
    public function testIt(): void
    {
        $factory = $this;
        $model = $factory->makeModel();

        $this->assertSame('done', $model->fresh()->status);
    }

    private function makeModel(): SomeModel
    {
        return SomeModel::create();
    }
}

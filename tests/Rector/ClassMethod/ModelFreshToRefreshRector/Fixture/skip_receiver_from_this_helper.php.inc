<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverFromThisHelperTest extends TestCase
{
    public function testIt(): void
    {
        $model = $this->makeModel();

        $this->assertSame('done', $model->fresh()->status);
    }

    private function makeModel(): SomeModel
    {
        return SomeModel::create();
    }
}

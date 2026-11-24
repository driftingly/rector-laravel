<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverFromCallOnVariableTest extends TestCase
{
    public function testIt(): void
    {
        $other = SomeModel::create();
        /** @var SomeModel $model */
        $model = $other->replicate();

        $this->assertSame('done', $model->fresh()->status);
    }
}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipUnsavedModelBuiltWithMakeTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = SomeModel::make();

        $this->assertSame('done', $model->fresh()->status);
    }
}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipClosureImportedReceiverTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $reload = function () use ($model): string {
            return $model->fresh()->status;
        };

        $this->assertSame('done', $reload());
    }

}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverImportedByReferenceInClosureTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $reset = function () use (&$model): void {
            $model = SomeModel::create();
        };

        $this->assertSame('done', $model->fresh()->status);

        $reset();
    }
}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipMatchSubjectTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $this->assertSame(1, match ($model->fresh()) {
            null => 0,
            default => 1,
        });
    }
}

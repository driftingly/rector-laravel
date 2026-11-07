<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultNegatedTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $this->assertFalse(! $model->fresh());
    }

}

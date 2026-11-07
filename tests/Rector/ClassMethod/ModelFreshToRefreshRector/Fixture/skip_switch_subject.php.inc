<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipSwitchSubjectTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        switch ($model->fresh()) {
            default:
                $this->addToAssertionCount(1);
        }
    }
}

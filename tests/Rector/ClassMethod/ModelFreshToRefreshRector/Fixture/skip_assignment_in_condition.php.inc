<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipAssignmentInConditionTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        if (! $fresh = $model->fresh()) {
            $this->fail('gone');
        }
    }
}

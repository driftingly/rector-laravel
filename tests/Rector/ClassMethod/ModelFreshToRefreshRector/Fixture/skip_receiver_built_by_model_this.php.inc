<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

final class SkipReceiverBuiltByModelThisTest extends SomeModel
{
    public function reloadedCopyStatus(): string
    {
        $copy = $this->copyOfSelf();

        return $copy->fresh()->status;
    }

    public function copyOfSelf(): static
    {
        return new static();
    }
}

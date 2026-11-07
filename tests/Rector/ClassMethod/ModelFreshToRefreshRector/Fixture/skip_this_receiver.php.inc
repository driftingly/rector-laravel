<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

final class SkipThisReceiverTest extends SomeModel
{
    public function reloadedStatus(): string
    {
        return $this->fresh()->status;
    }
}

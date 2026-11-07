<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverReturnedByMethodCallTest extends TestCase
{
    private function reload(bool $early): string
    {
        $model = SomeModel::create();
        if ($early) {
            return $model->getKeyName();
        }

        return $model->fresh()->status;
    }
}

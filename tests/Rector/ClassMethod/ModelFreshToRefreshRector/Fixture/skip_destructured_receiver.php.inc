<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipDestructuredReceiverTest extends TestCase
{
    private SomeModel $model;

    public function testIt(): void
    {
        $model = SomeModel::create();
        [$model] = [$this->model];

        $this->assertSame('done', $model->fresh()->status);
    }

}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipStaticVariableReceiverTest extends TestCase
{
    public function testIt(): void
    {
        static $model;

        $model = SomeModel::create();

        $this->assertSame('done', $model->fresh()->status);
    }

}

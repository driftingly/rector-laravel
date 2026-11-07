<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverFromConstructorNameOnNonModelTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = Registry::find(1);

        $this->assertSame('done', $model->fresh()->status);
    }
}

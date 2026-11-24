<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverIsFluentAliasTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $copy = $model->fill([]);

        $this->assertSame('done', $model->fresh()->status);
        $this->assertSame('pending', $copy->status);
    }

}

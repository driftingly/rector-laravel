<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverIsThisModelAliasTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $model */
        $model = $this;

        $this->assertSame('done', $model->fresh()->status);
    }
}

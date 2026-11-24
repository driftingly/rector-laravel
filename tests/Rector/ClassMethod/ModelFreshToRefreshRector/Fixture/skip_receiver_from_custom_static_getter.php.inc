<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverFromCustomStaticGetterTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $tenant */
        $tenant = SomeModel::current();

        $this->assertSame('done', $tenant->fresh()->status);
    }
}

<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverFromAuthHelperTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $user */
        $user = auth()->user();

        $this->assertSame('done', $user->fresh()->status);
    }
}

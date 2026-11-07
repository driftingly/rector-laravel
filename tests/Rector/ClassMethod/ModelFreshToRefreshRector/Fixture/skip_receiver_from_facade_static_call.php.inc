<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipReceiverFromFacadeStaticCallTest extends TestCase
{
    public function testIt(): void
    {
        /** @var SomeModel $user */
        $user = Auth::user();

        $this->assertSame('done', $user->fresh()->status);
    }
}

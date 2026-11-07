<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipVariableVariableReceiverTest extends TestCase
{
    public function testIt(): void
    {
        $name = 'model';

        $this->assertSame('done', $$name->fresh()->status);
    }
}

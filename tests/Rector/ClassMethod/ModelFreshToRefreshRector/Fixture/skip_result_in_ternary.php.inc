<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultInTernaryTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();

        $this->assertSame('a', $model->fresh() ? 'a' : 'b');
    }

}

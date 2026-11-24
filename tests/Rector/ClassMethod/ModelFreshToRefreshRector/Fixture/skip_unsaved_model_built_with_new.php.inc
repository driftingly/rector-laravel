<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipUnsavedModelBuiltWithNewTest extends TestCase
{
    public function testIt(): void
    {
        $model = new SomeModel();

        $this->assertSame('done', $model->fresh()->status);
    }
}

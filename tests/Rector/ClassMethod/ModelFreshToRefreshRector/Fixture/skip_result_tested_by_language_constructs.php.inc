<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultTestedByLanguageConstructsTest extends TestCase
{
    public function testResultCoalesced(): void
    {
        $model = SomeModel::create();

        $this->assertSame('none', $model->fresh()->status ?? 'none');
    }

    public function testResultIsset(): void
    {
        $model = SomeModel::create();

        $this->assertTrue(isset($model->fresh()->status));
    }

    public function testResultEmpty(): void
    {
        $model = SomeModel::create();

        $this->assertTrue(empty($model->fresh()));
    }

    public function testResultMethodCallCoalesced(): void
    {
        $model = SomeModel::create();
        $this->assertSame(1, $model->fresh()->getKeyName() ?? 1);
    }

    public function testResultArrayEntryCoalesced(): void
    {
        $model = SomeModel::create();
        $this->assertSame('none', $model->fresh()->meta['key'] ?? 'none');
    }
}

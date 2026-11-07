<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class VariableVariablesInScopeTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $name = 'status';
        $$name = 'pending';

        foreach (['a', 'b'] as $$name) {
            $this->addToAssertionCount(1);
        }

        $this->assertSame('pending', $$name);
        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class VariableVariablesInScopeTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $name = 'status';
        $$name = 'pending';

        foreach (['a', 'b'] as $$name) {
            $this->addToAssertionCount(1);
        }

        $this->assertSame('pending', $$name);
        $this->assertSame('done', $model->refresh()->status);
    }
}

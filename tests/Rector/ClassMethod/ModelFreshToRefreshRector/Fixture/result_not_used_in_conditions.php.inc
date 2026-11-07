<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ResultNotUsedInConditionsTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $attempts = 0;

        if ($attempts > 3) {
            $attempts = 3;
        } elseif ($attempts > 1) {
            $attempts = 1;
        }

        while ($attempts < 1) {
            $attempts++;
        }

        do {
            $attempts++;
        } while ($attempts < 2);

        for ($i = 0; $i < 3; $i++) {
            $attempts++;
        }

        for (;;) {
            break;
        }

        $this->assertSame('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class ResultNotUsedInConditionsTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $attempts = 0;

        if ($attempts > 3) {
            $attempts = 3;
        } elseif ($attempts > 1) {
            $attempts = 1;
        }

        while ($attempts < 1) {
            $attempts++;
        }

        do {
            $attempts++;
        } while ($attempts < 2);

        for ($i = 0; $i < 3; $i++) {
            $attempts++;
        }

        for (;;) {
            break;
        }

        $this->assertSame('done', $model->refresh()->status);
    }
}

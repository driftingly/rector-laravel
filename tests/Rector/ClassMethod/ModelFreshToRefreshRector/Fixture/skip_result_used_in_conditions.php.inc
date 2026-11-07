<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipResultUsedInConditionsTest extends TestCase
{
    public function testElseifCondition(): void
    {
        $model = SomeModel::create();
        $ready = false;

        if ($ready) {
            $this->addToAssertionCount(1);
        } elseif ($model->fresh()) {
            $this->addToAssertionCount(1);
        }
    }

    public function testWhileCondition(): void
    {
        $model = SomeModel::create();
        $freshModel = $model->fresh();

        while ($freshModel) {
            break;
        }
    }

    public function testDoWhileCondition(): void
    {
        $model = SomeModel::create();
        $freshModel = $model->fresh();

        do {
            $this->addToAssertionCount(1);
        } while ($freshModel);
    }

    public function testForCondition(): void
    {
        $model = SomeModel::create();
        $freshModel = $model->fresh();

        for ($i = 0; $freshModel; $i++) {
            break;
        }
    }

    public function testForWithSeveralConditions(): void
    {
        $model = SomeModel::create();
        $freshModel = $model->fresh();

        for ($i = 0; $i < 3, $freshModel; $i++) {
            break;
        }
    }

    public function testResultUsedAsCondition(): void
    {
        $model = SomeModel::create();

        if ($model->fresh()) {
            $this->addToAssertionCount(1);
        }
    }

    public function testAssignedResultUsedAsCondition(): void
    {
        $model = SomeModel::create();

        $freshModel = $model->fresh();

        if ($freshModel) {
            $this->addToAssertionCount(1);
        }
    }
}

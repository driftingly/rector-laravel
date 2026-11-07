<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

abstract class SkipAbstractMethodTest extends TestCase
{
    abstract protected function reloadedModel(): SomeModel;
}

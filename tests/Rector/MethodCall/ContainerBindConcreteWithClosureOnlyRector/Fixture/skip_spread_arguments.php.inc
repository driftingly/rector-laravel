<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Fixture;

use Illuminate\Contracts\Container\Container;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface;

/** @var Container $app */
/** @var array $binding */
$app->bind(...$binding);

$app->bind(SomeInterface::class, ...[function () {
    return new SomeClass();
}]);

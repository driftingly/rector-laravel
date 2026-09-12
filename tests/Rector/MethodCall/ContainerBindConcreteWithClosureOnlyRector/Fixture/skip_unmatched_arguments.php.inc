<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Fixture;

use Illuminate\Contracts\Container\Container;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface;

/** @var Container $app */
/** @var array $binding */
/** @var array $rest */
$app->bind(...$binding);

$app->bind(SomeInterface::class, ...[function () {
    return new SomeClass();
}]);

// dropping the abstract would take the spread with it
$app->bind(SomeInterface::class, function () {
    return new SomeClass();
}, ...$rest);

// singleton() has no $shared parameter, so the extra argument cannot be carried over
$app->singleton(SomeInterface::class, function () {
    return new SomeClass();
}, true);

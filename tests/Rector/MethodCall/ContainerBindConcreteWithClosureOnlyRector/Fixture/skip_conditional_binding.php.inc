<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Fixture;

use Illuminate\Contracts\Container\Container;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface;

// bindIf() and singletonIf() forward the abstract to bound(), which uses it as an
// array offset, so a closure abstract fatals there.
/** @var Container $app */
$app->bindIf(SomeInterface::class, function () {
    return new SomeClass();
});

$app->singletonIf(SomeInterface::class, function () {
    return new SomeClass();
});

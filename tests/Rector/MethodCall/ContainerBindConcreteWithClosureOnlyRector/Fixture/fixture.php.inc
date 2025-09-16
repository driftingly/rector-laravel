<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Fixture;

use Illuminate\Contracts\Container\Container;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface;

/** @var Container $app */
$app->bind(SomeInterface::class, function () {
    return new SomeClass();
});

$app->bind(SomeInterface::class, function (): SomeClass {
    return new SomeClass();
});

$app->singleton(SomeInterface::class, function () {
    return new SomeClass();
});

foreach ([SomeClass::class] as $item) {
    $app->singleton($item, function () use ($item) {
        return new $item();
    });
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Fixture;

use Illuminate\Contracts\Container\Container;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface;

/** @var Container $app */
$app->bind(function (): \RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface {
    return new SomeClass();
});

$app->bind(function (): \RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface {
    return new SomeClass();
});

$app->singleton(function (): \RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface {
    return new SomeClass();
});

foreach ([SomeClass::class] as $item) {
    $app->singleton($item, function () use ($item) {
        return new $item();
    });
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Fixture;

use Illuminate\Contracts\Container\Container;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface;

/** @var Container $app */
$app->bind(SomeInterface::class, function () {
    return new SomeClass();
}, true);

$app->bind(abstract: SomeInterface::class, concrete: function () {
    return new SomeClass();
}, shared: true);

// singleton() has no $shared parameter, so the inert extra argument is dropped
// rather than turned into a fatal `shared:` named argument
$app->singleton(SomeInterface::class, function () {
    return new SomeClass();
}, true);

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
}, shared: true);

$app->bind(function (): \RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface {
    return new SomeClass();
}, shared: true);

// singleton() has no $shared parameter, so the inert extra argument is dropped
// rather than turned into a fatal `shared:` named argument
$app->singleton(function (): \RectorLaravel\Tests\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector\Source\SomeInterface {
    return new SomeClass();
});

?>

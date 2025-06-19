<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture;

use RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model;

$factory->define(Model::class, function () {
    // ...
});

$factory->state(Model::class, 'address', function () {
    // ...
});

$factory->afterMaking(Model::class, function (Model $user) {
    // ...
});

$factory->afterCreating(Model::class, function () {
    // ...
});

?>

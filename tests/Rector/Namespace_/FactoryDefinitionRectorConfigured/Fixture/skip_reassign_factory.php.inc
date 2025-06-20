<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory = 1;
$factory->define(\App\User::class, function (\Faker\Generator $faker) {
    return [];
});

?>

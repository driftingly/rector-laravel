<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture;

use RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model;

$factory->define(Model::class, fn ($faker) => [
    'name' => $faker->name,
    'email' => $faker->unique()->safeEmail,
    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
]);

$factory->state(Model::class, 'address', fn ($faker) => [
    'address' => $faker->address,
]);

$factory->afterMaking(Model::class, fn (Model $user, $faker) => $user->name = $faker->name);

$factory->afterCreating(Model::class, fn (Model $user, $faker) => $user->name = $faker->name);

?>

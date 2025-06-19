<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture;

use RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model;

$factory->define(Model::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ];
});

$factory->state(Model::class, 'address', function ($faker) {
    return [
        'address' => $faker->address,
    ];
});

$factory->state(Model::class, 'delinquent', [
    'account_status' => 'delinquent',
]);

$factory->afterMaking(Model::class, function (Model $user, $faker) {
    $user->name = $faker->name;
});

$factory->afterCreating(Model::class, function ($user, $faker) {
    $user->name = $faker->name;
    $user->accounts()->save(factory(\App\Account::class)->make());
});

?>

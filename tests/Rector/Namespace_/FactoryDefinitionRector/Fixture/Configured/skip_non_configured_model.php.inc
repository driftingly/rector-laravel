<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\Configured;

use RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\AnotherModel;

$factory->define(AnotherModel::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ];
});

$factory->state(AnotherModel::class, 'address', function ($faker) {
    return [
        'address' => $faker->address,
    ];
});

$factory->state(AnotherModel::class, 'delinquent', [
    'account_status' => 'delinquent',
]);

$factory->afterMaking(AnotherModel::class, function (AnotherModel $user, $faker) {
    $user->name = $faker->name;
});

$factory->afterCreating(AnotherModel::class, function ($user, $faker) {
    $user->name = $faker->name;
    $user->accounts()->save(factory(\App\Account::class)->make());
});

?>

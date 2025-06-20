<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture;

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (\Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->define(Account::class, function (\Faker\Generator $faker) {
    return [
        'account' => Str::random(10),
    ];
});

?>

<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\Configured;

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
-----
<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\Configured;

use RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model;
class ModelFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = Model::class;
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];
    }
    public function address()
    {
        return $this->state(function () {
            return [
                'address' => $this->faker->address,
            ];
        });
    }
    public function delinquent()
    {
        return $this->state([
            'account_status' => 'delinquent',
        ]);
    }
    public function configure()
    {
        return $this->afterMaking(function (Model $user) {
            $user->name = $this->faker->name;
        })->afterCreating(function ($user) {
            $user->name = $this->faker->name;
            $user->accounts()->save(factory(\App\Account::class)->make());
        });
    }
}

?>

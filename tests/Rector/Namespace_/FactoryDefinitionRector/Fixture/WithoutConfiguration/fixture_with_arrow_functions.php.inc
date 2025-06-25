<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\WithoutConfiguration;

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
-----
<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\WithoutConfiguration;

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
        return $this->state(fn() => [
            'address' => $this->faker->address,
        ]);
    }
    public function configure()
    {
        return $this->afterMaking(fn (Model $user) => $user->name = $this->faker->name)->afterCreating(fn (Model $user) => $user->name = $this->faker->name);
    }
}

?>

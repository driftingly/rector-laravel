<?php
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'another_name' => $this->faker->name(),
            'another_email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
?>
-----
<?php
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'another_name' => fake()->name(),
            'another_email' => fake()->unique()->safeEmail(),
        ];
    }
}
?>

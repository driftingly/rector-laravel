<?php
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        $time = time();

        return [
            'foo_property' => "{$this->faker->uuid}.png",
            'foo_method' => "{$this->faker->uuid()}.png",
            'bar' => "{$this->faker->uuid}_{$time}.png",
            'baz' => "{$time}_{$this->faker->uuid}.png",
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
        $time = time();

        return [
            'foo_property' => fake()->uuid . ".png",
            'foo_method' => fake()->uuid() . ".png",
            'bar' => fake()->uuid . "_{$time}.png",
            'baz' => "{$time}_" . fake()->uuid . ".png",
        ];
    }
}
?>

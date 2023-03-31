<?php

use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->randomEnum(SomeEnum::class),
        ];
    }
}

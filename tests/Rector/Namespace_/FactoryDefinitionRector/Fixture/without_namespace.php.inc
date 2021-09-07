<?php

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

$factory->state(User::class, 'address', function ($faker) {
    return [
        'address' => $faker->address,
    ];
});

$factory->afterMaking(User::class, function (User $user, $faker) {
    // ...
});

$factory->afterCreating(User::class, function ($user, $faker) {
    $user->accounts()->save(factory(\App\Account::class)->make());
});

?>
-----
<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
class UserFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = User::class;
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
    public function address()
    {
        return $this->state(function (\array $attributes) {
            return [
                'address' => $this->faker->address,
            ];
        });
    }
    public function configure()
    {
        return $this->afterMaking(function (User $user) {
            // ...
        })->afterCreating(function ($user) {
            $user->accounts()->save(factory(\App\Account::class)->make());
        });
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sort' => $this->model::max('sort') + 1,
        ];
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sort' => $this->model::query()->max('sort') + 1,
        ];
    }
}
?>

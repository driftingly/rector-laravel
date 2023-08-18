<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveModelPropertyFromFactoriesRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveModelPropertyFromFactoriesRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
}

?>

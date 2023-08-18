<?php

namespace RectorLaravel\Tests\Rector\Class_\MoveFactoryModelPropertyToExtendsAnnotationRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = 'App\Models\User';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\MoveFactoryModelPropertyToExtendsAnnotationRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\WithoutConfiguration;

use RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model;

$factory->define(Model::class, function () {
    // ...
});

$factory->state(Model::class, 'address', function () {
    // ...
});

$factory->afterMaking(Model::class, function (Model $user) {
    // ...
});

$factory->afterCreating(Model::class, function () {
    // ...
});

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
        // ...
    }
    public function address()
    {
        return $this->state(function () {
            // ...
        });
    }
    public function configure()
    {
        return $this->afterMaking(function (Model $user) {
            // ...
        })->afterCreating(function () {
            // ...
        });
    }
}

?>

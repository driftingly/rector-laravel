<?php

use App;

$factory->define(RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
    ];
});

?>
-----
<?php

use App;

class ModelFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model::class;
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}

?>

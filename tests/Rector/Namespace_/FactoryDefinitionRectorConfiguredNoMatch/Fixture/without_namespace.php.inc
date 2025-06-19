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

$factory->define(RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
    ];
});

?>

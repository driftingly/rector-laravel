<?php

namespace Rector\Tests\TypeDeclaration\Rector\ReplaceFakerPropertyFetchWithMethodCallRector\Fixture;

use Faker\Generator;

function fake(): Generator
{
    return new Generator();
}

/** @var Generator $faker */
$faker->name;
fake()->name;

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\ReplaceFakerPropertyFetchWithMethodCallRector\Fixture;

use Faker\Generator;

function fake(): Generator
{
    return new Generator();
}

/** @var Generator $faker */
$faker->name();
fake()->name();

?>

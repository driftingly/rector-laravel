<?php

namespace Rector\Tests\TypeDeclaration\Rector\ReplaceFakerPropertyFetchWithMethodCallRector\Fixture;

use Faker\Generator;

$property = 'name';
/** @var Generator $faker */
$faker->$property;

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\ReplaceFakerPropertyFetchWithMethodCallRector\Fixture;

use Faker\Generator;

$property = 'name';
/** @var Generator $faker */
$faker->{$property}();

?>

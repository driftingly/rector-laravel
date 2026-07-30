<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Product;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

Product::factory()
    ->hasPhoto()
    ->has(Variation::factory()->times(2))
    ->create();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Product;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

Product::factory()
    ->hasPhoto()
    ->hasVariations(2)
    ->create();

?>

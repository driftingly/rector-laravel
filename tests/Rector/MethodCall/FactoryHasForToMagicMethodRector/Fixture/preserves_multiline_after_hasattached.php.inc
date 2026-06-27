<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Product;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

function makeWithAttached($tag)
{
    return Product::factory()
        ->hasAttached($tag)
        ->has(Variation::factory()->state(['online' => true]))
        ->create();
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Product;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

function makeWithAttached($tag)
{
    return Product::factory()
        ->hasAttached($tag)
        ->hasVariations(['online' => true])
        ->create();
}

?>

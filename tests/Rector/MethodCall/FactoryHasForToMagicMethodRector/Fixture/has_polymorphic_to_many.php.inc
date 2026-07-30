<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Label;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Product;

Product::factory()->has(Label::factory())->create();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Label;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Product;

Product::factory()->hasLabels()->create();

?>

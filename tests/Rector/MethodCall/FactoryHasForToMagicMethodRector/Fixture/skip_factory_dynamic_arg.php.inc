<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Product;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

Product::factory()->has(Variation::factory(random_int(1, 3)))->create();

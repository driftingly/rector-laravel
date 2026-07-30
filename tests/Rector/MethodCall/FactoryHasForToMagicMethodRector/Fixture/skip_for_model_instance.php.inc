<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Type;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

function makeVariationFor(Type $type): void
{
    Variation::factory()->for($type)->create();
}

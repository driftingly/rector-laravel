<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Type;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

Variation::factory()->for(Type::factory())->create();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Type;
use RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source\Variation;

Variation::factory()->forType()->create();

?>

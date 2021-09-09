<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\FactoryApplyingStatesRector\Fixture;

/** @var \Illuminate\Database\Eloquent\FactoryBuilder $factory */
$factory->states([]);
$factory->state('delinquent');
$factory->states('delinquent');
$factory->states('premium', 'delinquent');

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\FactoryApplyingStatesRector\Fixture;

/** @var \Illuminate\Database\Eloquent\FactoryBuilder $factory */
$factory;
$factory->delinquent();
$factory->delinquent();
$factory->premium()->delinquent();

?>

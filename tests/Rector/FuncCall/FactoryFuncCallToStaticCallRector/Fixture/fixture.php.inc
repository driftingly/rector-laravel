<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\Fixture;

factory(User::class);
factory(User::class, 1);

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\Fixture;

User::factory();
User::factory(1);

?>

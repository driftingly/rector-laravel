<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\Fixture;

factory(User::class);
factory(User::class, 1);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\Fixture;

factory(User::class);
factory(User::class, 1);

?>

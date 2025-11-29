<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\FooInterface;
use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

function (User&FooInterface $user) {
    $user::max('sort') + 1;
};

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\FooInterface;
use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

function (User&FooInterface $user) {
    $user::query()->max('sort') + 1;
};

?>

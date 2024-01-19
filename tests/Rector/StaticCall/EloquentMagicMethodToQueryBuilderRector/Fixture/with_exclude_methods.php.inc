<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\User;

class SomeController
{
    public function getUser()
    {
        # eligible
        $user = User::publicMethodBelongsToQueryBuilder();

        # not eligible
        $user = User::excludablePublicMethodBelongsToEloquentQueryBuilder();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\User;

class SomeController
{
    public function getUser()
    {
        # eligible
        $user = User::query()->publicMethodBelongsToQueryBuilder();

        # not eligible
        $user = User::excludablePublicMethodBelongsToEloquentQueryBuilder();
    }
}

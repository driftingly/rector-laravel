<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\User;

class SomeController
{
    public function getUser()
    {
        # eligible
        $user = User::publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = User::publicMethodBelongsToQueryBuilder(1);

        # not eligible
        $user = User::privateMethodBelongsToQueryBuilder(1);
        $user = User::protectedMethodBelongsToQueryBuilder(1);
        $user = User::publicMethodNotBelongsToQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToEloquentQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToQueryBuilder(1);
        $user = User::staticMethodBelongsToModel(1);
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
        $user = User::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = User::query()->publicMethodBelongsToQueryBuilder(1);

        # not eligible
        $user = User::privateMethodBelongsToQueryBuilder(1);
        $user = User::protectedMethodBelongsToQueryBuilder(1);
        $user = User::publicMethodNotBelongsToQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToEloquentQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToQueryBuilder(1);
        $user = User::staticMethodBelongsToModel(1);
    }
}

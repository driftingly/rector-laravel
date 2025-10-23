<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\User;

class SomeController
{
    public function __construct(
        protected readonly User $userbaseModel,
    ) {
    }

    public function getUser()
    {
        $userModel = new User;

        # eligible
        $user = User::publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = User::publicMethodBelongsToQueryBuilder(1);
        $user = $this->userbaseModel::publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = $userModel::publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();

        # not eligible
        $user = User::privateMethodBelongsToQueryBuilder(1);
        $user = User::protectedMethodBelongsToQueryBuilder(1);
        $user = User::publicMethodNotBelongsToQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToEloquentQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToQueryBuilder(1);
        $user = User::staticMethodBelongsToModel(1);
        $user = $this->userbaseModel::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = $userModel::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\User;

class SomeController
{
    public function __construct(
        protected readonly User $userbaseModel,
    ) {
    }

    public function getUser()
    {
        $userModel = new User;

        # eligible
        $user = User::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = User::query()->publicMethodBelongsToQueryBuilder(1);
        $user = $this->userbaseModel::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = $userModel::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();

        # not eligible
        $user = User::privateMethodBelongsToQueryBuilder(1);
        $user = User::protectedMethodBelongsToQueryBuilder(1);
        $user = User::publicMethodNotBelongsToQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToEloquentQueryBuilder(1);
        $user = User::query()->publicMethodBelongsToQueryBuilder(1);
        $user = User::staticMethodBelongsToModel(1);
        $user = $this->userbaseModel::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
        $user = $userModel::query()->publicMethodBelongsToEloquentQueryBuilder(1)->where('xxx', 'xxx')->first();
    }
}

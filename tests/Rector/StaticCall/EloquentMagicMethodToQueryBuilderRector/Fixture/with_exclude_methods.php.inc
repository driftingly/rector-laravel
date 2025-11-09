<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

class SomeController
{
    public function __construct(
        protected readonly User $userBaseModel,
    ) {
    }

    public function getUser()
    {
        $userModel = new User;

        # eligible
        $user = User::publicMethodBelongsToQueryBuilder();
        $user = $this->userBaseModel::publicMethodBelongsToQueryBuilder();
        $user = $userModel::publicMethodBelongsToQueryBuilder();

        # not eligible
        $user = User::excludablePublicMethodBelongsToEloquentQueryBuilder();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

class SomeController
{
    public function __construct(
        protected readonly User $userBaseModel,
    ) {
    }

    public function getUser()
    {
        $userModel = new User;

        # eligible
        $user = User::query()->publicMethodBelongsToQueryBuilder();
        $user = $this->userBaseModel::query()->publicMethodBelongsToQueryBuilder();
        $user = $userModel::query()->publicMethodBelongsToQueryBuilder();

        # not eligible
        $user = User::excludablePublicMethodBelongsToEloquentQueryBuilder();
    }
}

<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture\RemoveQuery;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

class Admin extends User
{
    public function test()
    {
        self::query()->excludablePublicMethodBelongsToEloquentQueryBuilder();
        static::query()->excludablePublicMethodBelongsToEloquentQueryBuilder();
        parent::query()->excludablePublicMethodBelongsToEloquentQueryBuilder();
    }
}

?>

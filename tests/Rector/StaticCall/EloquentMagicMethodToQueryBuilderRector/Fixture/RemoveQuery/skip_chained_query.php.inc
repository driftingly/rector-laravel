<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture\RemoveQuery;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

User::query()->where('active', true)->excludablePublicMethodBelongsToEloquentQueryBuilder();
User::query()->publicMethodBelongsToEloquentQueryBuilder();

?>

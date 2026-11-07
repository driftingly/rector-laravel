<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture\RemoveQuery;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\UserWithCustomQuery;

UserWithCustomQuery::query()->excludablePublicMethodBelongsToEloquentQueryBuilder();

?>

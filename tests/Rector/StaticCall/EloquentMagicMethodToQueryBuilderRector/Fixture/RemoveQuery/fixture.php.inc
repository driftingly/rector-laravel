<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture\RemoveQuery;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

User::query()->excludablePublicMethodBelongsToEloquentQueryBuilder(1);
User::excludablePublicMethodBelongsToEloquentQueryBuilder(1);
User::publicMethodBelongsToEloquentQueryBuilder(1);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Fixture\RemoveQuery;

use RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source\User;

User::excludablePublicMethodBelongsToEloquentQueryBuilder(1);
User::excludablePublicMethodBelongsToEloquentQueryBuilder(1);
User::query()->publicMethodBelongsToEloquentQueryBuilder(1);

?>

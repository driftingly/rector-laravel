<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture;

use Illuminate\Database\Query\Builder;

class Fixture
{
    public function run(Builder $query)
    {
        $query->where('name', 'like', 'Rector');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture;

use Illuminate\Database\Query\Builder;

class Fixture
{
    public function run(Builder $query)
    {
        $query->whereLike('name', 'Rector');
    }
}
?>

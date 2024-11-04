<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture;

use Illuminate\Database\Query\Builder;

class WithPostgresDriver
{
    public function run(Builder $query)
    {
        $query->where('name', 'like', 'Rector');
        $query->orWhere('name', 'like', 'Rector');
        $query->orwhere('name', 'LIKE', 'Rector');
        $query->where('name', 'not like', 'Rector');
        $query->orwhere('name', 'not like', 'Rector');
        $query->orwhere('name', like: 'not like', value: 'Rector');

        // Case Sensitivity
        $query->where('name', 'ilike', 'Rector');
        $query->where('name', 'not ilike', 'Rector');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture;

use Illuminate\Database\Query\Builder;

class WithPostgresDriver
{
    public function run(Builder $query)
    {
        $query->whereLike('name', 'Rector', true);
        $query->orWhereLike('name', 'Rector', true);
        $query->orWhereLike('name', 'Rector', true);
        $query->whereNotLike('name', 'Rector', true);
        $query->orWhereNotLike('name', 'Rector', true);
        $query->orWhereNotLike('name', value: 'Rector', caseSensitive: true);

        // Case Sensitivity
        $query->whereLike('name', 'Rector');
        $query->whereNotLike('name', 'Rector');
    }
}
?>

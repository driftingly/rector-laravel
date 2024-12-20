<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture\Postgres;

use Illuminate\Database\Query\Builder;

class WithPostgresDriver
{
    public function run(Builder $query, NonModel $nonQuery)
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
        $query->where('name', like: 'ilike', value: 'Rector');

        // Invalid
        $nonQuery->where('name', 'like', 'Rector');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture\Postgres;

use Illuminate\Database\Query\Builder;

class WithPostgresDriver
{
    public function run(Builder $query, NonModel $nonQuery)
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
        $query->whereLike('name', value: 'Rector');

        // Invalid
        $nonQuery->where('name', 'like', 'Rector');
    }
}
?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture;

use Illuminate\Database\Query\Builder;

class Fixture
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
        $query->where('name', 'like binary', 'Rector');
        $query->where('name', 'not like binary', 'Rector');
        $query->where('name', like: 'like binary', value: 'Rector');
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
        $query->orWhereLike('name', 'Rector');
        $query->orWhereLike('name', 'Rector');
        $query->whereNotLike('name', 'Rector');
        $query->orWhereNotLike('name', 'Rector');
        $query->orWhereNotLike('name', value: 'Rector');

        // Case Sensitivity
        $query->whereLike('name', 'Rector', true);
        $query->whereNotLike('name', 'Rector', true);
        $query->whereLike('name', value: 'Rector', caseSensitive: true);
    }
}
?>

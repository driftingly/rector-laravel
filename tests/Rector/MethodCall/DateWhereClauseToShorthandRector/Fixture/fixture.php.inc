<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Fixture;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

final class Post extends Model
{
}

final class SomeClass
{
    public function run(Builder $query, EloquentBuilder $eloquentBuilder): void
    {
        $query->where('published_at', '<', Carbon::now());
        $query->orWhere('published_at', '>=', Carbon::now());
        $query->whereDate('published_at', '=', Carbon::today());
        $query->orWhereDate('published_at', '<', Carbon::today());
        $query->whereDate('published_at', Carbon::today());

        $eloquentBuilder->where('published_at', '>', Carbon::now());

        Post::whereDate('published_at', '>=', Carbon::today());
        Post::orWhereDate('published_at', Carbon::today());

        $query->where('published_at', '=', Carbon::now());
        $query->whereDate('published_at', '!=', Carbon::today());
        $query->whereDate('published_at', Carbon::yesterday());
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Fixture;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

final class Post extends Model
{
}

final class SomeClass
{
    public function run(Builder $query, EloquentBuilder $eloquentBuilder): void
    {
        $query->wherePast('published_at');
        $query->orWhereNowOrFuture('published_at');
        $query->whereToday('published_at');
        $query->orWhereBeforeToday('published_at');
        $query->whereToday('published_at');

        $eloquentBuilder->whereFuture('published_at');

        Post::whereTodayOrAfter('published_at');
        Post::orWhereToday('published_at');

        $query->where('published_at', '=', Carbon::now());
        $query->whereDate('published_at', '!=', Carbon::today());
        $query->whereDate('published_at', Carbon::yesterday());
    }
}

?>

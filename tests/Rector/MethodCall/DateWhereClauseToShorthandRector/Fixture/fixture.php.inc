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
        $query->where('reviewed_at', '<=', now());
        $query->orWhere('published_at', '>=', Carbon::now());
        $query->whereDate('published_at', '=', Carbon::today());
        $query->whereDate('reviewed_at', '>=', today());
        $query->orWhereDate('published_at', '<', Carbon::today());
        $query->whereDate('published_at', Carbon::today());
        $query->orWhereDate('reviewed_at', today());

        $eloquentBuilder->where('published_at', '>', Carbon::now());
        $eloquentBuilder->whereDate('reviewed_at', '<=', today());

        Post::whereDate('published_at', '>=', Carbon::today());
        Post::orWhereDate('published_at', Carbon::today());
        Post::where('reviewed_at', '>', now());

        $query->where('published_at', '=', Carbon::now());
        $query->whereDate('published_at', '!=', Carbon::today());
        $query->whereDate('published_at', Carbon::yesterday());
        $query->whereDate('published_at', today('UTC'));
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
        $query->whereNowOrPast('reviewed_at');
        $query->orWhereNowOrFuture('published_at');
        $query->whereToday('published_at');
        $query->whereTodayOrAfter('reviewed_at');
        $query->orWhereBeforeToday('published_at');
        $query->whereToday('published_at');
        $query->orWhereToday('reviewed_at');

        $eloquentBuilder->whereFuture('published_at');
        $eloquentBuilder->whereTodayOrBefore('reviewed_at');

        Post::whereTodayOrAfter('published_at');
        Post::orWhereToday('published_at');
        Post::whereFuture('reviewed_at');

        $query->where('published_at', '=', Carbon::now());
        $query->whereDate('published_at', '!=', Carbon::today());
        $query->whereDate('published_at', Carbon::yesterday());
        $query->whereDate('published_at', today('UTC'));
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture\Default;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class SkipWithExpressions
{
    public function run(Builder $query)
    {
        $query->where('name', 'like', DB::raw('Rector'));
    }
}
?>

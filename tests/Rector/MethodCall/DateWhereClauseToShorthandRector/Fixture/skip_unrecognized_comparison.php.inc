<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Fixture;

use Illuminate\Database\Query\Builder;

final class SkipUnrecognizedComparison
{
    public function run(Builder $query): void
    {
        $query->where('published_at', 'like', now());
        $query->orWhere('published_at', '!=', now());

        $query->whereDate('published_at', 'like', today());
        $query->orWhereDate('published_at', '!=', today());
    }
}

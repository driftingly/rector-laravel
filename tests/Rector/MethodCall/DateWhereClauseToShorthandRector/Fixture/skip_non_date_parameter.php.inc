<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Fixture;

use Illuminate\Database\Query\Builder;

final class SkipNonDateParameter
{
    public function run(Builder $query): void
    {
        $query->where('published_at', '<', '2026-01-01 00:00:00');
        $query->orWhere('published_at', '>=', 123);

        $query->whereDate('published_at', '=', '2026-01-01');
        $query->orWhereDate('published_at', '<', 1);
    }
}

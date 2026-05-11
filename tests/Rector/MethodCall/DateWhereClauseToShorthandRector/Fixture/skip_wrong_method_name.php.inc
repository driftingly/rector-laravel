<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Fixture;

use Illuminate\Database\Query\Builder;
use RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Source\Post;

final class SkipWrongMethodName
{
    public function run(Builder $query): void
    {
        $query->having('published_at', '<', now());
        $query->orHaving('published_at', '>=', now());

        Post::having('published_at', '<', now());
    }
}

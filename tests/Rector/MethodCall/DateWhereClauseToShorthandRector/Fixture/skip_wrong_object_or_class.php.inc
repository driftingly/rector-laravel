<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Source\NonModel;

final class SkipWrongObjectOrClass
{
    public function run(object $notQuery): void
    {
        $notQuery->where('published_at', '<', now());

        NonModel::where('published_at', '<', now());
        NonModel::whereDate('published_at', '=', today());
    }
}

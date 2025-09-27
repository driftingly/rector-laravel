<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Database\Query\Builder;

/** @param 'asc'|'desc' $union */
function (Builder $query, string $dir, string $union) {
    $query->orderBy('created_at', $dir);
    $query->orderBy('created_at', $union);
};

<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Database\Query\Builder;

$asc = 'asc';

/** @var Builder $query */
$query->orderBy('created_at', $asc);

?>

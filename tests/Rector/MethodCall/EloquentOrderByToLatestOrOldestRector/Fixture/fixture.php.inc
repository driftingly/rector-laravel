<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Database\Query\Builder;

$column = 'tested_at';

/** @var Builder $query */
$query->orderBy('created_at');
$query->orderBy('created_at', 'desc');
$query->orderBy('submitted_at');
$query->orderByDesc('submitted_at');
$query->orderBy($renameable_variable_name);
$query->orderBy($unrenameable_variable_name);
$query->orderBy('disallowed_column_name');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Database\Query\Builder;

$column = 'tested_at';

/** @var Builder $query */
$query->oldest();
$query->latest();
$query->oldest('submitted_at');
$query->latest('submitted_at');
$query->oldest($renameable_variable_name);
$query->orderBy($unrenameable_variable_name);
$query->orderBy('disallowed_column_name');

?>

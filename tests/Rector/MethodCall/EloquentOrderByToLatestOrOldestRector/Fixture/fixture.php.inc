<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Database\Query\Builder;

$column = 'tested_at';

/** @var Builder $query */
$query->orderBy('created_at');
$query->orderBy('created_at', 'desc');
$query->orderBy('submitted_at');
$query->orderByDesc('submitted_at');
$query->orderBy($allowed_variable_name);

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
$query->oldest($allowed_variable_name);

?>

<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

$query->orderBy($unallowed_variable_name);
$query->orderBy('unallowed_column_name');

?>
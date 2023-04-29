<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Support\Facades\DB;

$string = (string) DB::raw('select 1');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Support\Facades\DB;

$string = DB::raw('select 1')->getValue(\Illuminate\Support\Facades\DB::connection()->getQueryGrammar());

?>

<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Support\Facades\DB;

$string = DB::raw('select 1')->__toString();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Support\Facades\DB;

$string = DB::raw('select 1')->getValue(\Illuminate\Support\Facades\DB::connection()->getQueryGrammar());

?>

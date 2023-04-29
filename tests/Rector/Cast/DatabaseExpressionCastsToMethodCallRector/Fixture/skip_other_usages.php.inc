<?php

namespace RectorLaravel\Tests\Rector\Cast\DatabaseExpressionCastsToMethodCall\Fixture;

use Illuminate\Support\Facades\DB;

DB::table('users')->where('name', DB::raw('select 1'))->get();

?>

<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

DB::table('users')
    ->where('active', true)
    ->orWhere('name', 'LIKE', 'Example%')
    ->orWhere('email', 'LIKE', 'Example%')
    ->orWhere('phone', 'LIKE', 'Example%')
    ->get();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

DB::table('users')->where('active', true)->whereAny(['name', 'email', 'phone'], 'LIKE', 'Example%')
    ->get();

?>

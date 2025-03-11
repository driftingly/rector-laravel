<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class BasicEqualsOperator
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('type', 'admin')
            ->orWhere('role', 'admin')
            ->orWhere('level', 'admin')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class BasicEqualsOperator
{
    public function run()
    {
        $users = DB::table('users')->where('active', true)->whereAny(['type', 'role', 'level'], '=', 'admin')
            ->get();
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithMixedEqualsArguments
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('type', 'admin')
            ->orWhere('role', '=', 'admin')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithMixedEqualsArguments
{
    public function run()
    {
        $users = DB::table('users')->where('active', true)->whereAny(['type', 'role'], '=', 'admin')
            ->get();
    }
}

?>

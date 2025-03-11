<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithTablePrefix
{
    public function run()
    {
        $users = DB::table('users as u')->where('u.active', true)
            ->orWhere('u.type', 'LIKE', '%admin%')
            ->orWhere('u.role', 'LIKE', '%admin%')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithTablePrefix
{
    public function run()
    {
        $users = DB::table('users as u')->where('u.active', true)->whereAny(['u.type', 'u.role'], 'LIKE', '%admin%')
            ->get();
    }
}

?>

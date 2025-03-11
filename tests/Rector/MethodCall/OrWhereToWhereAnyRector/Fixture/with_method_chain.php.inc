<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithMethodChain
{
    public function run()
    {
        $users = DB::table('users')
            ->select('id', 'name')
            ->where('active', true)
            ->orWhere('type', 'LIKE', '%admin%')
            ->orWhere('role', 'LIKE', '%admin%')
            ->orderBy('name')
            ->limit(10)
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithMethodChain
{
    public function run()
    {
        $users = DB::table('users')
            ->select('id', 'name')->where('active', true)->whereAny(['type', 'role'], 'LIKE', '%admin%')
            ->orderBy('name')
            ->limit(10)
            ->get();
    }
}

?>

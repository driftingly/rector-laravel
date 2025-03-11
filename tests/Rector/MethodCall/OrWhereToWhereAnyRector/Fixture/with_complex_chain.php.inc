<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithComplexChain
{
    public function run()
    {
        $users = DB::table('users')
            ->select('*')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.active', true)
            ->orWhere('users.type', 'LIKE', '%admin%')
            ->orWhere('users.role', 'LIKE', '%admin%')
            ->orderBy('users.created_at', 'desc')
            ->groupBy('users.id')
            ->having('count(*)', '>', 1)
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class WithComplexChain
{
    public function run()
    {
        $users = DB::table('users')
            ->select('*')
            ->join('roles', 'users.role_id', '=', 'roles.id')->where('users.active', true)->whereAny(['users.type', 'users.role'], 'LIKE', '%admin%')
            ->orderBy('users.created_at', 'desc')
            ->groupBy('users.id')
            ->having('count(*)', '>', 1)
            ->get();
    }
}

?>

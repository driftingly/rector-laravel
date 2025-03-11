<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipDifferentValues
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('type', 'LIKE', '%admin%')
            ->orWhere('role', 'LIKE', '%user%')
            ->get();
    }
}
?>

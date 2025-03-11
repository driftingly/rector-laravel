<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipMixedArguments
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('type', 'LIKE', '%admin%')  // LIKE operator
            ->orWhere('role', '=', 'admin')       // = operator
            ->orWhere('status', 'active')         // implicit = operator
            ->get();
    }
}
?> 
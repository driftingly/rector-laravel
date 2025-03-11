<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipInvalidArgs
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere()  // invalid - no args
            ->orWhere('type')  // invalid - one arg
            ->get();
    }
}
?> 
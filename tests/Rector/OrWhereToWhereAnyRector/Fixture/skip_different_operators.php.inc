<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipDifferentOperators
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('age', '>', 18)
            ->orWhere('status', '=', 'active')
            ->get();
    }
}
?> 
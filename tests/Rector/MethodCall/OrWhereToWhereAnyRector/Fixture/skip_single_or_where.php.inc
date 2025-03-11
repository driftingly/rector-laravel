<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipSingleOrWhere
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('type', 'admin')
            ->get();
    }
}
?>

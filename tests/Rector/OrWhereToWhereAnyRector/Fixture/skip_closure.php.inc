<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipClosure
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere(function($query) {
                $query->where('type', 'admin');
            })
            ->get();
    }
}
?> 
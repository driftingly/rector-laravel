<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipNestedQueries
{
    public function run()
    {
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('type', 'admin')
            ->orWhere(function($query) {
                $query->where('role', 'admin')
                    ->orWhere('level', 'admin');
            })
            ->get();
    }
}
?> 
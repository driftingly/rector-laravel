<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class SkipVariableValues
{
    public function run()
    {
        $searchTerm = 'admin';
        $users = DB::table('users')
            ->where('active', true)
            ->orWhere('type', $searchTerm)
            ->orWhere('role', 'admin')  // Mixed static and variable values
            ->get();
    }
}
?> 
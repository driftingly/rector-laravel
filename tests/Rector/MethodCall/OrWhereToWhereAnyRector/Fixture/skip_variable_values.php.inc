<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipVariableValues
{
    public function run(Builder $builder)
    {
        $searchTerm = 'admin';
        $users = $builder
            ->where('active', true)
            ->orWhere('type', $searchTerm)
            ->orWhere('role', 'admin')  // Mixed static and variable values
            ->get();
    }
}
?>

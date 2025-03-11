<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipMixedArguments
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('type', 'LIKE', '%admin%')  // LIKE operator
            ->orWhere('role', '=', 'admin')       // = operator
            ->orWhere('status', 'active')         // implicit = operator
            ->get();
    }
}
?>

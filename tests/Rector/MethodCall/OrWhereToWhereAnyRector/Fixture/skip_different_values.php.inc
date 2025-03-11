<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipDifferentValues
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('type', 'LIKE', '%admin%')
            ->orWhere('role', 'LIKE', '%user%')
            ->get();
    }
}
?>

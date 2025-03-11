<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipInvalidArgs
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere()  // invalid - no args
            ->orWhere('type')  // invalid - one arg
            ->get();
    }
}
?>

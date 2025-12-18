<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipFirstClassCallable
{
    public function test(Builder $query)
    {
        $query->where(...);
    }
}
?>

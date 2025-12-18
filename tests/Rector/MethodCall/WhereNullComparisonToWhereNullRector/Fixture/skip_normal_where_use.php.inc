<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipNormalWhereUse
{
    public function test(Builder $query)
    {
        $query->where('foo', 'bar')
            ->where('bar', '=', 'bar');
    }
}
?>

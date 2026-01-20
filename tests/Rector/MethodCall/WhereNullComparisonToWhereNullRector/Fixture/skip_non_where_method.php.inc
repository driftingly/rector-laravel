<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipNonWhereMethod
{
    public function test(Builder $query)
    {
        $query->whereFoo('foo', null)
            ->whereFoo('bar', '=', null);
    }
}
?>

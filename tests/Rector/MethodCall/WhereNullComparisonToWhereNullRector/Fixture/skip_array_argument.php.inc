<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipArrayArgument
{
    public function test(Builder $query)
    {
        $query->where(['name' => 'foo', 'active' => true])->exists();
    }
}
?>

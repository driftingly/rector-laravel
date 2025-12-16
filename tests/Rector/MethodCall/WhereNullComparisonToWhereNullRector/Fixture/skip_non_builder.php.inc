<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class SkipNonBuilder
{
    public function test($query)
    {
        $query->where('foo', 'bar')
            ->where('bar', '=', 'bar');
    }
}
?>

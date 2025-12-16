<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class Fixture
{
    public function test(Builder $query)
    {
        $query->where('foo', null)
            ->where('bar', '=', null);
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class Fixture
{
    public function test(Builder $query)
    {
        $query->whereNull('foo')->whereNull('bar');
    }
}
?>

<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithMixedEqualsArguments
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('type', 'admin')
            ->orWhere('role', '=', 'admin')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithMixedEqualsArguments
{
    public function run(Builder $builder)
    {
        $users = $builder->where('active', true)->whereAny(['type', 'role'], '=', 'admin')
            ->get();
    }
}

?>

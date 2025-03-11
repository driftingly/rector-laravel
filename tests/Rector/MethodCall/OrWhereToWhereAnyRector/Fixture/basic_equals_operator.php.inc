<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class BasicEqualsOperator
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('type', 'admin')
            ->orWhere('role', 'admin')
            ->orWhere('level', 'admin')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class BasicEqualsOperator
{
    public function run(Builder $builder)
    {
        $users = $builder->where('active', true)->whereAny(['type', 'role', 'level'], '=', 'admin')
            ->get();
    }
}

?>

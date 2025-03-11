<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithMethodChain
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('type', 'LIKE', '%admin%')
            ->orWhere('role', 'LIKE', '%admin%')
            ->orderBy('name')
            ->limit(10)
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithMethodChain
{
    public function run(Builder $builder)
    {
        $users = $builder->where('active', true)->whereAny(['type', 'role'], 'LIKE', '%admin%')
            ->orderBy('name')
            ->limit(10)
            ->get();
    }
}

?>

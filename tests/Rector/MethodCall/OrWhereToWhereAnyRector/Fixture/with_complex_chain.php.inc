<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithComplexChain
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('users.active', true)
            ->orWhere('users.type', 'LIKE', '%admin%')
            ->orWhere('users.role', 'LIKE', '%admin%')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->orderBy('users.created_at', 'desc')
            ->groupBy('users.id')
            ->having('count(*)', '>', 1)
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithComplexChain
{
    public function run(Builder $builder)
    {
        $users = $builder->where('users.active', true)->whereAny(['users.type', 'users.role'], 'LIKE', '%admin%')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->orderBy('users.created_at', 'desc')
            ->groupBy('users.id')
            ->having('count(*)', '>', 1)
            ->get();
    }
}

?>

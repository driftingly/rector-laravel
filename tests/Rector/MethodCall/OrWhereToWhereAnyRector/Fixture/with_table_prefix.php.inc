<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithTablePrefix
{
    public function run(Builder $builder)
    {
        $users = $builder->where('u.active', true)
            ->orWhere('u.type', 'LIKE', '%admin%')
            ->orWhere('u.role', 'LIKE', '%admin%')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class WithTablePrefix
{
    public function run(Builder $builder)
    {
        $users = $builder->where('u.active', true)->whereAny(['u.type', 'u.role'], 'LIKE', '%admin%')
            ->get();
    }
}

?>

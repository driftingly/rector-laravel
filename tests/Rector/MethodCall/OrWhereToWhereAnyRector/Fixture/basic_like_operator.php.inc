<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class BasicLikeOperator
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->orWhere('name', 'LIKE', 'Example%')
            ->orWhere('email', 'LIKE', 'Example%')
            ->orWhere('phone', 'LIKE', 'Example%')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class BasicLikeOperator
{
    public function run(Builder $builder)
    {
        $users = $builder->whereAny(['name', 'email', 'phone'], 'LIKE', 'Example%')
            ->get();
    }
}

?>

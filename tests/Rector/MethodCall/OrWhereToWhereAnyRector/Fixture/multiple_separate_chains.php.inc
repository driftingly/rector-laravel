<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class MultipleSeparateChains
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('name', 'LIKE', 'John%')
            ->orWhere('email', 'LIKE', 'John%')
            ->where('verified', true)
            ->orWhere('city', 'New York')
            ->orWhere('state', 'New York')
            ->where('country', 'US')
            ->orWhere('type', 'premium')
            ->orWhere('level', 'premium')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class MultipleSeparateChains
{
    public function run(Builder $builder)
    {
        $users = $builder->where('active', true)->whereAny(['name', 'email'], 'LIKE', 'John%')->where('verified', true)->whereAny(['city', 'state'], '=', 'New York')->where('country', 'US')->whereAny(['type', 'level'], '=', 'premium')
            ->get();
    }
}

?>

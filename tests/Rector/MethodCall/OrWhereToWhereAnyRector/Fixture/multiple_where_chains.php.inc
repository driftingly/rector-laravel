<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class MultipleWhereChains
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('name', 'LIKE', 'John%')
            ->orWhere('email', 'LIKE', 'John%')
            ->where('age', '>', 18)
            ->orWhere('city', 'London')
            ->orWhere('country', 'London')
            ->get();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class MultipleWhereChains
{
    public function run(Builder $builder)
    {
        $users = $builder->where('active', true)->whereAny(['name', 'email'], 'LIKE', 'John%')->where('age', '>', 18)->whereAny(['city', 'country'], '=', 'London')
            ->get();
    }
}

?>

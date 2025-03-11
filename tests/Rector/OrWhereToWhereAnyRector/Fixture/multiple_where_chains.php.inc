<?php

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class MultipleWhereChains
{
    public function run()
    {
        $users = DB::table('users')
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

namespace RectorLaravel\Tests\Rector\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Support\Facades\DB;

class MultipleWhereChains
{
    public function run()
    {
        $users = DB::table('users')->where('active', true)->whereAny(['name', 'email'], 'LIKE', 'John%')->where('age', '>', 18)->whereAny(['city', 'country'], '=', 'London')
            ->get();
    }
}

?>

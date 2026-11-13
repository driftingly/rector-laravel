<?php

namespace RectorLaravel\Tests\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector\Fixture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
}

function typed(int $count, array $state)
{
    User::factory($count);
    User::factory($state);
    User::factory($count, $state);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector\Fixture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
}

function typed(int $count, array $state)
{
    User::factory()->count($count);
    User::factory()->state($state);
    User::factory()->count($count)->state($state);
}

?>

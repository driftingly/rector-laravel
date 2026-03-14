<?php

namespace RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [
        'is_admin',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Guarded([
    'is_admin',
])]
class User extends Model
{
}

?>

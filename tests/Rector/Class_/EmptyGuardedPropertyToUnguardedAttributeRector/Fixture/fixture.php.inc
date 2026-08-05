<?php

namespace RectorLaravel\Tests\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Unguarded]
class User extends Model
{
}

?>

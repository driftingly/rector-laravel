<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $incrementing = false;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\WithoutIncrementing]
class User extends Model
{
}

?>

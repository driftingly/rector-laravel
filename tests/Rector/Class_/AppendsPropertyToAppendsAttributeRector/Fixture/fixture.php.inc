<?php

namespace RectorLaravel\Tests\Rector\Class_\AppendsPropertyToAppendsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $appends = [
        'full_name',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AppendsPropertyToAppendsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Appends([
    'full_name',
])]
class User extends Model
{
}

?>

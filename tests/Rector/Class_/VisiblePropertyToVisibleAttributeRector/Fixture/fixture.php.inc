<?php

namespace RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $visible = [
        'name',
        'email',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Visible([
    'name',
    'email',
])]
class User extends Model
{
}

?>

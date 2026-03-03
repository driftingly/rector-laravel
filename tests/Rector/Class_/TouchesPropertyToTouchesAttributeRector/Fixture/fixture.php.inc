<?php

namespace RectorLaravel\Tests\Rector\Class_\TouchesPropertyToTouchesAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $touches = [
        'posts',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TouchesPropertyToTouchesAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Touches([
    'posts',
])]
class User extends Model
{
}

?>

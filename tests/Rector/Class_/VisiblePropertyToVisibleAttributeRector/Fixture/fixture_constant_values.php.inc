<?php

namespace RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class UserWithConstantVisibleValues extends Model
{
    private const FIELD_NAME = 'name';

    private const FIELD_EMAIL = 'email';

    protected $visible = [
        self::FIELD_NAME,
        self::FIELD_EMAIL,
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Visible([
    self::FIELD_NAME,
    self::FIELD_EMAIL,
])]
class UserWithConstantVisibleValues extends Model
{
    private const FIELD_NAME = 'name';

    private const FIELD_EMAIL = 'email';
}

?>

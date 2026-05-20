<?php

namespace RectorLaravel\Tests\Rector\Class_\DateFormatPropertyToDateFormatAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $dateFormat = 'U';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\DateFormatPropertyToDateFormatAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\DateFormat('U')]
class User extends Model
{
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\TimestampsPropertyToTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class TimestampsFalse extends Model
{
    public $timestamps = false;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TimestampsPropertyToTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\WithoutTimestamps]
class TimestampsFalse extends Model
{
}

?>

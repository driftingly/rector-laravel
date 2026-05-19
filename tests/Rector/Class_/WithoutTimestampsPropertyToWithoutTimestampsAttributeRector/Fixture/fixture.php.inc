<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    public $timestamps = false;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\WithoutTimestamps]
class EventLog extends Model
{
}

?>

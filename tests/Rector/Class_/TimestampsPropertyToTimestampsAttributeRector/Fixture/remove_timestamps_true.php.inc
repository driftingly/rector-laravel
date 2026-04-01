<?php

namespace RectorLaravel\Tests\Rector\Class_\TimestampsPropertyToTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class TimestampsTrue extends Model
{
    public $timestamps = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TimestampsPropertyToTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class TimestampsTrue extends Model
{
}

?>

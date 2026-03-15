<?php

namespace RectorLaravel\Tests\Rector\Class_\AppendsPropertyToAppendsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonStringAppends extends Model
{
    protected $appends = [
        1,
    ];
}

?>

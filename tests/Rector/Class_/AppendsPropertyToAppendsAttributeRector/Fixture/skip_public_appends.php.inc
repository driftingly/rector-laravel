<?php

namespace RectorLaravel\Tests\Rector\Class_\AppendsPropertyToAppendsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipPublicAppends extends Model
{
    public $appends = [
        'full_name',
    ];
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\AppendsPropertyToAppendsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Appends(['full_name'])]
class SkipExistingAttribute extends Model
{
    protected $appends = [
        'full_name',
    ];
}

?>

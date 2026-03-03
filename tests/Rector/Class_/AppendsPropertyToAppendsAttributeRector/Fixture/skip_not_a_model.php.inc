<?php

namespace RectorLaravel\Tests\Rector\Class_\AppendsPropertyToAppendsAttributeRector\Fixture;

class SkipNotAModel
{
    protected $appends = [
        'full_name',
    ];
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNotAModel
{
    protected $visible = [
        'name',
        'email',
    ];
}

?>

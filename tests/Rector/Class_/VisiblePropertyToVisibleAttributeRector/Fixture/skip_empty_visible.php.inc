<?php

namespace RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipEmptyVisible extends Model
{
    protected $visible = [];
}

?>

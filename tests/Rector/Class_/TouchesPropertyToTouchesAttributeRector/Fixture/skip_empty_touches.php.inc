<?php

namespace RectorLaravel\Tests\Rector\Class_\TouchesPropertyToTouchesAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipEmptyTouches extends Model
{
    protected $touches = [];
}

?>

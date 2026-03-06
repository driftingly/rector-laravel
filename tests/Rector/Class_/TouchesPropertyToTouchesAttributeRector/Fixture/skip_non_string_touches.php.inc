<?php

namespace RectorLaravel\Tests\Rector\Class_\TouchesPropertyToTouchesAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonStringTouches extends Model
{
    protected $touches = [
        1,
    ];
}

?>

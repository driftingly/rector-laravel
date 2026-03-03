<?php

namespace RectorLaravel\Tests\Rector\Class_\TouchesPropertyToTouchesAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipPublicTouches extends Model
{
    public $touches = [
        'posts',
    ];
}

?>

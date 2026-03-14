<?php

namespace RectorLaravel\Tests\Rector\Class_\TouchesPropertyToTouchesAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Touches(['posts'])]
class SkipExistingAttribute extends Model
{
    protected $touches = [
        'posts',
    ];
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\VisiblePropertyToVisibleAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Visible(['name', 'email'])]
class SkipExistingAttribute extends Model
{
    protected $visible = [
        'name',
        'email',
    ];
}

?>

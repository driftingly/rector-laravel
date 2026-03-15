<?php

namespace RectorLaravel\Tests\Rector\Class_\HiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Hidden(['password'])]
class SkipExistingAttribute extends Model
{
    protected $hidden = [
        'password',
    ];
}

?>

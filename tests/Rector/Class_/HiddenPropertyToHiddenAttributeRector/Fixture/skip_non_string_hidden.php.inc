<?php

namespace RectorLaravel\Tests\Rector\Class_\HiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NonStringHidden extends Model
{
    protected $hidden = [
        'password',
        123,
    ];
}

?>

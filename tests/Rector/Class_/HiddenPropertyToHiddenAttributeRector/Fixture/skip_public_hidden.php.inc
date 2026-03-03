<?php

namespace RectorLaravel\Tests\Rector\Class_\HiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class PublicHidden extends Model
{
    public $hidden = ['password'];
}

?>

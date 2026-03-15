<?php

namespace RectorLaravel\Tests\Rector\Class_\HiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipEmptyHidden extends Model
{
    protected $hidden = [];
}

?>

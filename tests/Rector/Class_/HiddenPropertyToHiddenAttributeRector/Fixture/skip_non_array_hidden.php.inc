<?php

namespace RectorLaravel\Tests\Rector\Class_\HiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NonArrayHidden extends Model
{
    protected $hidden = 'password';
}

?>

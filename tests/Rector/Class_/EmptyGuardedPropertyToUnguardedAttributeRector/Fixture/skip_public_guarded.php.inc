<?php

namespace RectorLaravel\Tests\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipPublicGuarded extends Model
{
    public $guarded = [];
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipEmptyGuarded extends Model
{
    protected $guarded = [];
}

?>

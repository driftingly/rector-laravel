<?php

namespace RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonStringGuarded extends Model
{
    protected $guarded = [
        1,
    ];
}

?>

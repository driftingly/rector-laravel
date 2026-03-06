<?php

namespace RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipPublicGuarded extends Model
{
    public $guarded = [
        'is_admin',
    ];
}

?>

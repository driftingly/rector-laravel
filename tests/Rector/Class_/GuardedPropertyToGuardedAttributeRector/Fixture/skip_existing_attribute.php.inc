<?php

namespace RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Guarded(['is_admin'])]
class SkipExistingAttribute extends Model
{
    protected $guarded = [
        'is_admin',
    ];
}

?>

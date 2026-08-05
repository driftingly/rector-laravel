<?php

namespace RectorLaravel\Tests\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonEmptyGuarded extends Model
{
    protected $guarded = [
        'is_admin',
    ];
}

?>

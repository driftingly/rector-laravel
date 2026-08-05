<?php

namespace RectorLaravel\Tests\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipWithFillable extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    protected $guarded = [];
}

?>

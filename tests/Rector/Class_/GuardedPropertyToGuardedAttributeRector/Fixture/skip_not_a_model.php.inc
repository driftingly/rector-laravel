<?php

namespace RectorLaravel\Tests\Rector\Class_\GuardedPropertyToGuardedAttributeRector\Fixture;

class SkipNotAModel
{
    protected $guarded = [
        'is_admin',
    ];
}

?>

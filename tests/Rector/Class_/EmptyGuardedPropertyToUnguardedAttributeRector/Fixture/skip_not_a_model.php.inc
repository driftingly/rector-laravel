<?php

namespace RectorLaravel\Tests\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector\Fixture;

class NotAModel
{
    protected $guarded = [];
}

?>

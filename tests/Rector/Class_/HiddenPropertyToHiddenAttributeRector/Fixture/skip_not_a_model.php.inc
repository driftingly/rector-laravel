<?php

namespace RectorLaravel\Tests\Rector\Class_\HiddenPropertyToHiddenAttributeRector\Fixture;

class NotAModel
{
    protected $hidden = ['password'];
}

?>

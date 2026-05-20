<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNotAModel
{
    public $incrementing = false;
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipIncrementingTrue extends Model
{
    public $incrementing = true;
}

?>

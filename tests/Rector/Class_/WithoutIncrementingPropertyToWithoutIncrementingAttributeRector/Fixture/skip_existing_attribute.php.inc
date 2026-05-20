<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\WithoutIncrementing]
class SkipExistingAttribute extends Model
{
    public $incrementing = false;
}

?>

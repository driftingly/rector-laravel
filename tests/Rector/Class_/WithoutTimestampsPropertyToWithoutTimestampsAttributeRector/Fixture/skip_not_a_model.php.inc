<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNotAModel
{
    public $timestamps = false;
}

?>

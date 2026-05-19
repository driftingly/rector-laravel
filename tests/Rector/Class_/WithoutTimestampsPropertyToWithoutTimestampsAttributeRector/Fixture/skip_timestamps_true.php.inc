<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipTimestampsTrue extends Model
{
    public $timestamps = true;
}

?>

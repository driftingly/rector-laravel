<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\WithoutTimestamps]
class SkipExistingAttribute extends Model
{
    public $timestamps = false;
}

?>

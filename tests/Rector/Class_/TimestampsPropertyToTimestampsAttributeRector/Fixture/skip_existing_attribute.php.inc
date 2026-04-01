<?php

namespace RectorLaravel\Tests\Rector\Class_\TimestampsPropertyToTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[Illuminate\Database\Eloquent\Attributes\WithoutTimestamps]
class ExistingAttribute extends Model
{
}

?>
